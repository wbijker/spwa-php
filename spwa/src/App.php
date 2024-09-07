<?php

namespace Spwa;

use Spwa\Dom\HtmlNode;
use Spwa\Dom\Levenshtein;
use Spwa\Template\Component;
use Spwa\Template\NodePath;
use Spwa\Template\PathState;

class App
{
    static function render(Component $component): void
    {
        // render previous
        $state = new PathState();
        $view = $component->view();
        $prev = $view->render(NodePath::root(), $state);

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
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
        $handler = $state->getEvent(new NodePath([0, 2, 0]), "click");
        if ($handler) {
            $handler();
        }

        // render again with potential new changes
        $next = $component->view()->render(NodePath::root(), $state);

        // compare the $prev and $next render instances
        /** @var Patch[] $patches */
        $patches = [];
        HtmlNode::compareNodes($prev, $next, $patches);

        // and finally return the patches to the JS runtime
        echo json_encode($patches);
    }
}


