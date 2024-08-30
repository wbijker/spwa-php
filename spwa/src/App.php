<?php

namespace Spwa;

use Spwa\Dom\HtmlNode;
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

        // find event from frontend.
        // execute event that will likely change the dom
        $handler = $state->getEvent(new NodePath([0, 2, 0]), "click");
        if ($handler) {
            $handler();
        }

        // render again
        $next = $component->view()->render(NodePath::root(), $state);

        /** @var Patch[] $patches */
        $patches = [];
        HtmlNode::compareNodes($prev, $next, $patches);

//        echo $next->render();
        echo $prev->render();

        echo PHP_EOL . "<script>" . PHP_EOL;
        echo PHP_EOL . "const patches = " . json_encode($patches) . ";" . PHP_EOL;
        include __DIR__ . "/js/spwa-runtime.js";
        echo PHP_EOL . "</script>" . PHP_EOL;

    }
}


