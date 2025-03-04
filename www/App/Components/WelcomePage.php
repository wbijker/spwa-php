<?php

namespace App\Components;

use Spwa\Html\HtmlDocument;
use Spwa\Html\Meta;
use Spwa\Html\Script;
use Spwa\Html\Title;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\State;
use Spwa\Route\Route;
use Spwa\Route\Router;

/* return new Div(children: [
            new InputText(class: "m-2 p-2 border", value: $this->text, onInput: fn($value) => $this->text = $value),
            new Div(class: "m-2 p-2 border bg-orange-200", children: [
                new HtmlText("Reversed: " . $this->reverse())
            ]),
        ]);
*/;


class WelcomePage extends Component
{

    function render(): HtmlNode
    {
        return new HtmlDocument(
            lang: "en",
            head: [
                new Title("Some document"),
                new Meta(charset: "UTF-8"),
                new Meta(name: "viewport", content: "width=device-width, initial-scale=1.0"),
                new Script(src: "https://cdn.tailwindcss.com"),
                new Script(src: "/assets/spwa.js"),
            ],
            body: $this->body()
        );
    }

    private Counter|null $last = null;

    #[State]
    private string $text = "Hap";

    private function reverse(): string
    {
        return strrev($this->text);
    }

    function body(): Node
    {
        return new Router(routes: [
            new Route(path: "", component: new AboutPage()),
            new Route(path: ProductRoute::class, component: fn(ProductRoute $product) => new ProductsPage($product)),
        ], fallback: new AboutPage());


        /*
                $other = new Counter();

                return new Div(children: [
                    new Div(children: [
                        new HtmlText("0,0,0"),
                    ]),
                    new HtmlText("0,1"),

                    // if condition
        //            $this->text == "hap"
        //                ? new Div(class: "hap-true", children: [new HtmlText("true")])
        //                : new Div(class: "hap-false", children: [new HtmlText("false")]),

                    new InputText(class: "m-2 p-2 border", value: $this->text, onInput: fn($value) => $this->text = $value),

                    new Div(class: "m-2 p-2 border bg-orange-200", children: [new HtmlText("Reversed: " . $this->reverse())]),

                    new Div(children: array_map(fn($i) => new Div(key: "#" . $i, children: [
                        new HtmlText("Item $i render"),
                        new Counter(onChange: function ($value) use ($other) {
        //                    $this->last?->setCounter($value);
        //                    $other->setCounter($value);
                        }),
                    ]), [5, 6])),

        //            new Div(class: "last", children: [
        //                new HtmlText("Last "),
        //            ]),

        //            new Counter(ref: function ($instance) {
        //                $this->last = $instance;
        //            }),
        //            $other
                ]);*/
    }
}