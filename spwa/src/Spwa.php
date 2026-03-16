<?php

namespace Spwa;

use Spwa\Debug\DebugPanel;
use Spwa\Js\JsRuntime;
use Spwa\State\StateManager;
use Spwa\UI\StyleGenerator;
use Spwa\UI\TagDomNode;
use Spwa\VNode\App;
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
     * @param StateManager[] $states
     */
    private static function handlePost(App $entry, StateManager $primaryState, array $states): void
    {
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

        // Render the old tree, execute event, save state
        $oldApp = new ($entry::class)();
        $oldUi = $oldApp->render($primaryState, null, RenderPhase::Initial);

        // Hydrate bound input values from the frontend
        if (!empty($bindings) && $oldUi instanceof TagDomNode) {
            $oldUi->hydrateBindings($bindings);
        }

        $node = $oldUi->findByPath($path);
        if ($node !== null) {
            $node->executeEvent($event, $primaryState, $value);
        }

        $oldApp->finalize($primaryState);

        // Render the new tree with updated state
        $newApp = new ($entry::class)();
        $newUi = $newApp->render($primaryState, null, RenderPhase::Patch);

        // Diff
        $patcher = new Patcher();
        $newUi->compare($oldUi, $patcher);

        // Styles delta
        $oldStyles = $oldUi->collectStyles();
        $newStyles = $newUi->collectStyles();
        $deltaStyles = StyleGenerator::delta($oldStyles, $newStyles);
        $deltaGenerator = StyleGenerator::from($deltaStyles);

        // Capture buffered output → console.log
        $output = ob_get_clean();
        if ($output !== '' && $output !== false) {
            JsRuntime::invoke(['console', 'log'], [$output]);
        }

        // Debug panel → console (prepended so it appears first)
        $appCalls = JsRuntime::drain();
        (new DebugPanel($newUi, $states))->emit();
        $debugCalls = JsRuntime::drain();
        JsRuntime::prepend(array_merge($debugCalls, $appCalls));

        $response = [
            'success' => true,
            'js' => JsRuntime::dump(),
            'patches' => $patcher->getOperations(),
            'styles' => $deltaGenerator->toRaw(),
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
        $ui = $entry->render($primaryState, null, RenderPhase::Initial);
        $entry->finalize($primaryState);

        $generator = StyleGenerator::from($ui->collectStyles());
        $stateJs = self::getClientJs($states);

        // Debug panel → inline script for initial render
        (new DebugPanel($ui, $states))->emit();
        $debugJs = self::callsToJs(JsRuntime::drain());

        $head = (new TagDomNode('head'))
            ->content(
                (new TagDomNode('meta'))->attr('charset', 'UTF-8'),
                (new TagDomNode('meta'))->attr('name', 'viewport')->attr('content', 'width=device-width, initial-scale=1.0'),
                (new TagDomNode('title'))->rawContent(htmlspecialchars($entry->title())),
                (new TagDomNode('style'))->attr('id', 'spwa-styles')->rawContent($generator->toStyle()),
                (new TagDomNode('script'))->attr('src', 'spwa.js')->rawContent(''),
            );

        if ($stateJs !== null) {
            $head->content((new TagDomNode('script'))->rawContent($stateJs));
        }

        $body = (new TagDomNode('body'))
            ->attr('style', 'margin: 0; font-family: system-ui, -apple-system, sans-serif;')
            ->content($ui);

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
