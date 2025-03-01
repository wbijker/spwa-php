<?php

namespace App\Components;

use Spwa\Html\A;
use Spwa\Html\Div;
use Spwa\Html\MouseEvents;
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
                url: (new ProductRoute(category: "Electronics", product: 44, limit: 10))->toUrl(),
                text: "Kettle"
            )
        ]);
    }
}