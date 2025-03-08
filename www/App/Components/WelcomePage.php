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
use Spwa\Route\RoutePath;
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
            new Route(path: Routes::$about, component: new AboutPage()),
            new Route(path: Routes::$product, component: fn(ProductRoute $product) => new ProductsPage($product)),
        ], fallback: new AboutPage());

    }
}

