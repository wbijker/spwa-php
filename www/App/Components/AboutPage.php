<?php

namespace App\Components;

use Spwa\Html\Div;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;

class AboutPage extends Component
{
    function render(): Node
    {
        return new Div(children: [
            new HtmlText("About page")
        ]);
    }
}