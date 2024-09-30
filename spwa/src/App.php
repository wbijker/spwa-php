<?php

namespace Spwa;

use Spwa\Dom\HtmlNode;
use Spwa\Js\JsRuntime;
use Spwa\Template\NodePath;
use Spwa\Template\Page;
use Spwa\Template\PathState;
use Spwa\Template\StateHandler;

class App
{

    static private function save(StateHandler $handler, Page $page, PathState $state): void
    {
        $handler->save(serialize([
                $page->saveState(),
                $state->saveComponents()]
        ));
    }

    static private function restore(StateHandler $handler, Page $page, PathState $state): void
    {
        $data = $handler->restore();
        if ($data != null) {
            [$pageState, $components] = unserialize($data);
            $state->restoreComponents($components);
            $page->restoreState($pageState);
        }
    }

    static private function handlePost(PathState $state)
    {
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
    }

    static function render(Page $page): void
    {
        ob_start();
        $root = NodePath::root();
        $stateHandler = $page->stateHandler();
        $stateHandler->initialize();

        $state = new PathState();
        self::restore($stateHandler, $page, $state);

        // render previous
        $view = $page->view();
        $prev = $view->render($root, $state);

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            self::save($stateHandler, $page, $state);
            echo $prev->render();
            return;
        }

        self::handlePost($state);

        self::save($stateHandler, $page, $state);
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


