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

    function body(): Node
    {
        return new Div(children: [
            new HtmlText("Counters"),

            new ForNode([1,2,3,4,5], fn($i) => $i * 2, fn($i) =>
                new Div(class: "child-1", children: [
                    new Div(children: [
                        new HtmlText("Item $i render"),
                    ]),
                    new Counter(),
                ])),
            new Div(class: "last", children: [
                new HtmlText("Last "),
            ]),
        ]);

//        return new Div(class: "h-screen w-screen flex", children: [
//            new Div(class: "m-auto", children: [
//                new Div(
//                    children: [
//                        new HtmlText("Counters"),
//                        new Counter(),
//                    ])
//            ])
//        ]);
    }
}