<?php

namespace App\Components;

use Spwa\Html\A;
use Spwa\Html\Div;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Route\RouteLink;

class ProductsPage extends Component
{
    public function __construct(private ProductRoute $product)
    {
    }

    function render(): Node
    {
        return new Div(children: [
            new Div(children: [new HtmlText("Category: " . $this->product->category)]),
            new Div(children: [new HtmlText("Product: " . $this->product->product)]),
            new Div(children: [new HtmlText("Limit: " . $this->product->limit)]),

            new A(class: "underline mx-2 cursor-pointer", href: "/", children: [new HtmlText("Home")]),

            new RouteLink(
                url: (new ProductRoute(category: "Clothes", product: 12, limit: 10))->toUrl(),
                text: "Clothes"
            ),

            new RouteLink(
                url: (new ProductRoute(category: "Electronics", product: 44, limit: 10))->toUrl(),
                text: "Electronics"
            ),

            new RouteLink(
                url: (new ProductRoute(category: "Appliances", product: 52, limit: 10))->toUrl(),
                text: "Appliances"
            )
        ]);
    }
}