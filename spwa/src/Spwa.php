<?php

namespace Spwa;

use Spwa\Debug\DebugPanel;
use Spwa\Debug\Timings;
use Spwa\Js\JsRuntime;
use Spwa\State\StateManager;
use Spwa\UI\StyleGenerator;
use Spwa\UI\TagDomNode;
use Spwa\VNode\App;
use Spwa\VNode\Component;
use Spwa\VNode\Patcher;
use Spwa\VNode\RenderPhase;

class Spwa
{
    public static function run(App $entry): void
    {
        $states = $entry->states();
        $primaryState = $entry->getDefaultState();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::handlePost($entry, $primaryState, $states);
        } else {
            self::handleGet($entry, $primaryState, $states);
        }
    }

    /**
     * Drop every state manager's stored state. Used to recover from a
     * shape mismatch between serialized state and the current code.
     * @param StateManager[] $states
     */
    private static function clearAllStates(array $states): void
    {
        foreach ($states as $state) {
            $state->clearAll();
        }
    }

    /**
     * Fingerprint the combined contents of every state manager.
     * Used as a cheap optimistic-concurrency token: the frontend echoes
     * back the hash from page render, the backend re-hashes the stored
     * state before processing an event, and a mismatch forces a reload.
     * @param StateManager[] $states
     */
    private static function computeStateHash(array $states): string
    {
        $combined = [];
        foreach ($states as $i => $state) {
            $combined[$i] = $state->getAll();
        }
        return sha1(serialize($combined));
    }

    /**
     * @param StateManager[] $states
     */
    private static function handlePost(App $entry, StateManager $primaryState, array $states): void
    {
        $t = new Timings();
        ob_start();

        // Parse payload from JSON or multipart form
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'multipart/form-data')) {
            $payload = json_decode($_POST['_spwa'] ?? '{}', true);
        } else {
            $payload = json_decode(file_get_contents('php://input'), true);
        }

        $event = $payload['event'] ?? '';
        $pathStr = $payload['path'] ?? '';
        $path = array_map('intval', explode(',', $pathStr));
        $value = $payload['value'] ?? null;
        $bindings = $payload['bindings'] ?? [];
        $expectedHash = $payload['hash'] ?? null;
        $t->mark('parse_payload');

        // Optimistic concurrency: the frontend echoes the hash it was
        // rendered against. If the backend's current state hashes differently
        // (e.g. another tab mutated it, or a deploy reshaped it), the frontend
        // is operating on a stale tree — bail out and force a reload.
        if ($expectedHash !== null && $expectedHash !== self::computeStateHash($states)) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'reload' => true]);
            exit;
        }
        $t->mark('verify_hash');

        // Render the old tree, execute event, save state. If the render or
        // event handler crashes because serialized state no longer matches
        // the current code's shape, clear all state and tell the client to
        // reload — the fresh page will start from defaults.
        try {
            $oldApp = new ($entry::class)();
            $oldUi = $oldApp->render($primaryState, null, RenderPhase::DiffOld);
            $t->mark('render_old');

            if (!empty($bindings) && $oldUi instanceof TagDomNode) {
                $oldUi->hydrateBindings($bindings);
            }
            $t->mark('hydrate_bindings');

            $node = $oldUi->findByPath($path);
            if ($node !== null) {
                $node->executeEvent($event, $primaryState, $value);
            }
            $t->mark('execute_event');

            $oldApp->finalize($primaryState);
            $t->mark('finalize_old');

            $newApp = new ($entry::class)();
            $newUi = $newApp->render($primaryState, null, RenderPhase::Patch);
            $t->mark('render_new');
        } catch (\Throwable $e) {
            self::clearAllStates($states);
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'reload' => true]);
            exit;
        }

        // Lifecycle: deleted (old tree components not in new tree)
        Component::processDeleted();
        $t->mark('process_deleted');

        // Diff
        $patcher = new Patcher();
        $newUi->compare($oldUi, $patcher);
        $t->mark('diff');

        // Styles delta
        $oldStyles = $oldUi->collectStyles();
        $newStyles = $newUi->collectStyles();
        $deltaStyles = StyleGenerator::delta($oldStyles, $newStyles);
        $deltaGenerator = StyleGenerator::from($deltaStyles);
        $t->mark('styles_delta');

        // Capture buffered output → console.log
        $output = ob_get_clean();
        if ($output !== '' && $output !== false) {
            JsRuntime::invoke(['console', 'log'], [$output]);
        }

        $newHash = self::computeStateHash($states);
        $t->mark('compute_hash');

        // Debug panel → console (prepended so it appears first)
        $appCalls = JsRuntime::drain();
        (new DebugPanel($newUi, $states, $t))->emit();
        $debugCalls = JsRuntime::drain();
        JsRuntime::prepend(array_merge($debugCalls, $appCalls));

        $response = [
            'success' => true,
            'js' => JsRuntime::dump(),
            'patches' => $patcher->getOperations(),
            'styles' => $deltaGenerator->toRaw(),
            'hash' => $newHash,
        ];

        $clientState = self::getClientState($states);
        if ($clientState !== null) {
            $response['state'] = $clientState;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    /**
     * @param StateManager[] $states
     */
    private static function handleGet(App $entry, StateManager $primaryState, array $states): void
    {
        $t = new Timings();

        // If restoring serialized state crashes the render, drop all state
        // and retry from defaults. A second failure is propagated.
        try {
            $ui = $entry->render($primaryState, null, RenderPhase::Initial);
            $entry->finalize($primaryState);
        } catch (\Throwable $e) {
            self::clearAllStates($states);
            $entry = new ($entry::class)();
            $ui = $entry->render($primaryState, null, RenderPhase::Initial);
            $entry->finalize($primaryState);
        }
        $t->mark('render');

        // Render the optional loader overlay before collecting styles so its
        // CSS lands in the same <style> block.
        $loaderVNode = $entry->getLoader();
        $loaderDom = $loaderVNode?->render($primaryState, null, RenderPhase::Initial);
        $t->mark('render_loader');

        $styles = $ui->collectStyles();
        if ($loaderDom !== null) {
            $styles = array_merge($styles, $loaderDom->collectStyles());
        }
        $generator = StyleGenerator::from($styles);
        $t->mark('collect_styles');

        $stateJs = self::getClientJs($states);

        // Collect custom CSS/JS registered by components
        $customCss = implode("\n", $entry->getCustomCss());
        $customJs = implode("\n", $entry->getCustomJs());

        $stateHash = self::computeStateHash($states);
        $t->mark('compute_hash');

        // Debug panel → inline script for initial render. Construct AFTER
        // timings so far so they appear in the debug output.
        (new DebugPanel($ui, $states, $t))->emit();
        $debugJs = self::callsToJs(JsRuntime::drain());

        $head = (new TagDomNode('head'))
            ->content(
                (new TagDomNode('meta'))->attr('charset', 'UTF-8'),
                (new TagDomNode('meta'))->attr('name', 'viewport')->attr('content', 'width=device-width, initial-scale=1.0'),
                (new TagDomNode('title'))->rawContent(htmlspecialchars($entry->title())),
                (new TagDomNode('script'))->rawContent('window.__SPWA_HASH=' . json_encode($stateHash) . ';'),
                (new TagDomNode('style'))->attr('id', 'spwa-styles')->rawContent($generator->toStyle()),
                (new TagDomNode('style'))->attr('id', 'spwa-custom-styles')->rawContent($customCss),
                (new TagDomNode('script'))->attr('src', 'spwa.js')->rawContent(''),
                (new TagDomNode('script'))->rawContent($customJs),
            );

        if ($stateJs !== null) {
            $head->content((new TagDomNode('script'))->rawContent($stateJs));
        }

        $body = (new TagDomNode('body'))
            ->attr('style', 'margin: 0; font-family: system-ui, -apple-system, sans-serif;')
            ->content($ui);

        // Optional loader overlay — sibling of the App root so it isn't part
        // of the diff tree. Hidden by default; the frontend toggles its
        // display while a request is in flight.
        if ($loaderDom !== null) {
            $loaderWrapper = (new TagDomNode('div'))
                ->attr('data-spwa-loader', '')
                ->attr('style', 'display:none')
                ->content($loaderDom);
            $body->content($loaderWrapper);
        }

        $body->content((new TagDomNode('script'))->rawContent($debugJs));

        $document = (new TagDomNode('html'))
            ->attr('lang', 'en')
            ->content($head, $body);

        echo '<!DOCTYPE html>' . $document->toHtml();
    }

    /**
     * @param StateManager[] $states
     */
    private static function getClientJs(array $states): ?string
    {
        $js = '';
        foreach ($states as $manager) {
            $managerJs = $manager->getClientJs();
            if ($managerJs !== null) {
                $js .= $managerJs . "\n";
            }
        }
        return $js === '' ? null : $js;
    }

    /**
     * Convert raw JsRuntime call entries to inline JavaScript.
     */
    private static function callsToJs(array $calls): string
    {
        $js = '';
        foreach ($calls as [$mode, $path, $args]) {
            $pathStr = implode('.', $path);
            if ($mode === 'invoke') {
                $argsJson = implode(',', array_map(fn($a) => json_encode($a, JSON_UNESCAPED_SLASHES), $args));
                $js .= $pathStr . '(' . $argsJson . ');';
            }
        }
        return $js;
    }

    /**
     * @param StateManager[] $states
     */
    private static function getClientState(array $states): ?array
    {
        $result = [];
        foreach ($states as $manager) {
            $clientState = $manager->getClientState();
            if ($clientState !== null) {
                $result[] = $clientState;
            }
        }
        return empty($result) ? null : array_merge(...$result);
    }
}
