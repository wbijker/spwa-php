<?php

namespace Spwa;

use Spwa\Dom\HtmlNode;
use Spwa\Js\JS;
use Spwa\Js\JsRuntime;
use Spwa\Template\NodePath;
use Spwa\Template\Page;
use Spwa\Template\PathState;

class App
{
    static function render(Page $page): void
    {
        ob_start();
        $stateHandler = $page->stateHandler();
        $stateHandler->initialize();

        $data = $stateHandler->restore();
        $state = new PathState();


        if ($data != null) {
            $state->restoreComponents(unserialize($data));
        }

        // render previous
        $view = $page->view();
        $prev = $view->render(NodePath::root(), $state);

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $stateHandler->save(serialize($state->saveComponents()));
            echo $prev->render();
            return;
        }

        // read JSON body
        $json = json_decode(file_get_contents('php://input'), true);

        $event = $json['event'];
        $inputs = $json["inputs"];

        // handle bindings
        foreach ($inputs as $path => $value) {
            $pathData = $state->get(new NodePath(json_decode($path)));
            if ($pathData->binding)
                $pathData->binding->set($value);
        }

        [$path, $event] = $event;
        // find event from frontend.
        // execute event that will likely change the dom
        $handler = $state->get(new NodePath($path))->getEvent($event);
        if ($handler) {
            $handler();
        }

        $stateHandler->save(serialize($state->saveComponents()));

        // render again with potential new changes
        $next = $page->view()->render(NodePath::root(), $state);

        // compare the $prev and $next render instances
        /** @var Patch[] $patches */
        $patches = [];
        HtmlNode::compareNodes($prev, $next, $patches);

        // and finally return the patches to the JS runtime
        echo json_encode([
            'p' => $patches,
            'j' => JsRuntime::dump()
        ]);
    }
}


