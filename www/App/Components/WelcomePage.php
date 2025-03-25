<?php

namespace App\Components;

use App\Db\Product;
use CodeQuery\Columns\FloatColumn;
use CodeQuery\Columns\StringColumn;
use CodeQuery\Query;
use Spwa\Html\Div;
use Spwa\Html\ExternalScript;
use Spwa\Html\Img;
use Spwa\Html\Meta;
use Spwa\Html\Table;
use Spwa\Html\Td;
use Spwa\Html\Title;
use Spwa\Html\Tr;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Nodes\Page;
use Spwa\Nodes\State;
use Spwa\Route\Route;
use Spwa\Route\Router;

class WelcomePage extends Page
{
    #[State]
    /** @var Selection[] $items */
    private array $items = [];

    /**
     * @return Selection[]
     */
    function sir(): array
    {
        $p = new Product();

        return Query::from($p)
            ->where($p->category_id->equals(3))
            ->orderBy($p->price)
            ->orderBy($p->category_id)
            ->select([
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price->multiply(2)
            ])
            ->fetch(Selection::class);
    }

    function initialized(): void
    {
        $this->items = $this->sir();
    }


    function renderBody(): Node
    {
        return new Div(class: "flex w-screen h-screen", children: [

            new Div(class: "m-auto", children: [
                new Div(class: "bg-white p-8 border-y", children: [
                    new Img(src: "/assets/images/logo.png", alt: "b", style: ['width' => '200px']),
                    new Router(routes: [
                        new Route(path: Routes::$about, component: new AboutPage()),
                        new Route(path: Routes::$product, component: fn(ProductRoute $product) => new ProductsPage($product)),
                    ], fallback: new AboutPage()),

                    new Table(children: [
                        new Tr(children: [
                            new Td(children: [new HtmlText("Id")]),
                            new Td(children: [new HtmlText("Name")]),
                            new Td(children: [new HtmlText("Price")]),
                        ]
                        ),
                        ...array_map(fn(Selection $item) => new Tr(children: [
                            new Td(children: [new HtmlText($item->id)]),
                            new Td(children: [new HtmlText($item->name)]),
                            new Td(children: [new HtmlText($item->price)])
                        ]), $this->items),
                    ]),
                ]),

                new Div(class: "text-right text-gray-400 text-xs py-2", children: [
                    new HtmlText("BrickPHP v1.0.0")
                ])
            ])
        ]);
    }

    function header(): array
    {
        return [
            new Title("Some document"),
            new Meta(charset: "UTF-8"),
            new Meta(name: "viewport", content: "width=device-width, initial-scale=1.0"),
            new ExternalScript(src: "https://cdn.tailwindcss.com"),
        ];
    }

}
