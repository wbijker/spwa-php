<?php

namespace Spwa;

use Spwa\Js\JsRuntime;
use Spwa\State\StateManagers;
use Spwa\UI\StyleGenerator;
use Spwa\UI\TagDomNode;
use Spwa\VNode\App;
use Spwa\VNode\Patcher;
use Spwa\VNode\RenderPhase;

class Spwa
{
    public static function run(App $entry): void
    {
        StateManagers::init();
        $state = StateManagers::$session;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::handlePost($entry, $state);
        } else {
            self::handleGet($entry, $state);
        }
    }

    private static function handlePost(App $entry, $state): void
    {
        ob_start();

        $payload = json_decode(file_get_contents('php://input'), true);
        $event = $payload['event'] ?? '';
        $pathStr = $payload['path'] ?? '';
        $path = array_map('intval', explode(',', $pathStr));
        $value = $payload['value'] ?? null;

        // Render the old tree, execute event, save state
        $oldApp = new ($entry::class)();
        $oldUi = $oldApp->render($state, null, RenderPhase::Initial);

        $node = $oldUi->findByPath($path);
        if ($node !== null) {
            $node->executeEvent($event, $state, $value);
        }

        $oldApp->finalize($state);

        // Render the new tree with updated state
        $newApp = new ($entry::class)();
        $newUi = $newApp->render($state, null, RenderPhase::Patch);

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
        ];

        $clientState = StateManagers::getClientState();
        if ($clientState !== null) {
            $response['state'] = $clientState;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    private static function handleGet(App $entry, $state): void
    {
        $ui = $entry->render($state, null, RenderPhase::Initial);
        $entry->finalize($state);

        $generator = StyleGenerator::from($ui->collectStyles());
        $stateJs = StateManagers::getClientJs();

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
}
