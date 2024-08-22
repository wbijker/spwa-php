<?php

namespace Spwa;

use Spwa\Template\Node;

class App
{
    static function render(Node $component): void
    {
        $html = $component->execute();
        echo $html->render();
    }
}