<?php

namespace App\Components;

use Spwa\Html\Div;
use Spwa\Html\HtmlDocument;
use Spwa\Html\Meta;
use Spwa\Html\MouseEvents;
use Spwa\Html\Script;
use Spwa\Html\Title;
use Spwa\Nodes\Component;
use Spwa\Nodes\ForNode;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Nodes\Page;

class WelcomePage extends Page
{
    function render(): HtmlDocument
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

    private $count = 1;

    function body(): Node
    {
        return new Div(children: [
            new Div(children: [
                new HtmlText("0,0,0"),
            ]),
            new HtmlText("0,1"),

            // if condition
//            $this->count < 12
//                ? new HtmlText("12")
//                : null,

            // new for
            new Div(children: array_map(fn($i) => new Div(key: $i * 2, children: [
                new HtmlText("Item $i render"),
                new Counter()
            ]), [1, 2, 3, 4, 5])
            ),

            new Div(class: "last", children: [
                new HtmlText("Last "),
            ]),
        ]);
    }
}