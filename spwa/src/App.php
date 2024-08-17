<?php

namespace Spwa;

use Spwa\Html\HtmlTagNode;
use Spwa\Template\Node;

class App
{
    static function render(Node $component): void
    {
        // we need a entry node to start all rendering
        $body = new HtmlTagNode("body", [], []);
        $component->execute($body);

        echo $body->render();
    }
}