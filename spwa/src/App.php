<?php

namespace Spwa;

use Spwa\Dom\HtmlNode;
use Spwa\Js\JsVar;
use Spwa\Template\NodePath;
use Spwa\Template\Page;
use Spwa\Template\PathState;

class App
{
    static function render(Page $page): void
    {
        $page->init();
//        $page->stateHandler();

        if ($_COOKIE['state']) {
            $data = unserialize($_COOKIE['state']);
            $page->restore($data);
        }
        // render previous
        $state = new PathState();
        $view = $page->view();
        $prev = $view->render(NodePath::root(), $state);

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            setcookie('state', serialize($page->save()));
            echo $prev->render();
            return;
        }

        // read JSON body
        $json = json_decode(file_get_contents('php://input'), true);

        $event = $json['event'];
        if (!isset($event)) {
            echo "event not found";
            return;
        }

        [$path, $event] = $event;

        // find event from frontend.
        // execute event that will likely change the dom
        $handler = $state->getEvent(new NodePath($path), $event);
        if ($handler) {
            $handler();
        }

        setcookie('state', serialize($page->save()));

        // render again with potential new changes
        $next = $page->view()->render(NodePath::root(), $state);

        // compare the $prev and $next render instances
        /** @var Patch[] $patches */
        $patches = [];
        HtmlNode::compareNodes($prev, $next, $patches);

        // and finally return the patches to the JS runtime
        echo json_encode($patches);
    }
}


