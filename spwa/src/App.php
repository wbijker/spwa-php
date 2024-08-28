<?php

namespace Spwa;

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
        $next = $component->view();
        echo $next->render(NodePath::root(), $state)->render();
    }
}


