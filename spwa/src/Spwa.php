<?php

namespace Spwa;

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

        $payload = json_decode(file_get_contents('php://input'), true);
        $event = $payload['event'] ?? '';
        $pathStr = $payload['path'] ?? '';
        $path = array_map('intval', explode(',', $pathStr));
        $value = $payload['value'] ?? null;

        // Render the old tree, execute event, save state
        $oldApp = new ($entry::class)();
        $oldUi = $oldApp->render($primaryState, null, RenderPhase::Initial);

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

        $response = [
            'success' => true,
            'js' => JsRuntime::dump(),
            'patches' => $patcher->getOperations(),
            'styles' => $deltaGenerator->toRaw(),
            'debug' => [
                'nodes' => $newUi->countNodes(),
                'states' => array_map(fn(StateManager $s) => [
                    'name' => $s->name(),
                    'bytes' => $s->bytes(),
                ], $states),
            ],
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
