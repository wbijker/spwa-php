<?php

namespace App\Components;

use App\Db\Product;
use CodeQuery\Columns\FloatColumn;
use CodeQuery\Columns\IntColumn;
use CodeQuery\Expressions\ConstExpression;
use CodeQuery\Queryable\Aggregation;
use CodeQuery\Queryable\Query;
use Spwa\Html\Div;
use Spwa\Html\Img;
use Spwa\Html\Meta;
use Spwa\Html\Table;
use Spwa\Html\Td;
use Spwa\Html\Title;
use Spwa\Html\Tr;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Nodes\Page;
use Spwa\Route\Route;
use Spwa\Route\Router;


class WelcomePage extends Page
{
//    #[State]
    /** @var Selection[] $items */
    private array $items = [];

    /**
     * @template T
     * @param class-string<T> $class
     * @return T[]
     */
    function fetch(string $class): array
    {
        return [];
    }

    function test(array &$arr): array
    {
        $arr['test'] = new ConstExpression(44);
        return $arr;
    }

    /**
     * @return Selection[]
     */
    function sir(): array
    {
        $p = new Product();

        /* @var object{category_id: IntColumn, count: IntColumn, sum: FloatColumn, int: IntColumn} $sub */
        $sub = (object)[
            'category_id' => $p->category_id,
            'count' => $p->category_id->count(),
            'sum' => $p->price->sum(),
            'countAll' => Aggregation::star()->count(),
        ];

        $q = Query::from($p)
            ->where($p->category_id->greaterThan(3))
            ->groupBy($p->category_id)
            ->select($sub)
            ->fetch(Selection::class);

        return [];
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
//            new ExternalScript(src: "https://cdn.tailwindcss.com"),
        ];
    }

}
