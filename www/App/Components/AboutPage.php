<?php

namespace App\Components;

use Spwa\Html\Div;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Route\RouteLink;


class AboutPage extends Component
{
    function render(): Node
    {
        return new Div(children: [
            new HtmlText("About page"),

            new RouteLink(
                href: Routes::$product->toUrl(new ProductRoute("electronics", 44, 50)),
                text: "Kettle"
            )
        ]);
    }
}