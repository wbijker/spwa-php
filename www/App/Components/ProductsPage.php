<?php

namespace App\Components;

use Spwa\Html\A;
use Spwa\Html\Div;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;

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
            new A(class: "underline mx-2 cursor-pointer", href: (new ProductRoute(category: "Electronics", product: 33, limit: 10))->toUrl(), children: [new HtmlText("Home")]),
        ]);
    }
}