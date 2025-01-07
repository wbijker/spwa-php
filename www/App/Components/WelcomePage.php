<?php

namespace App\Components;

use Spwa\Html\Div;
use Spwa\Html\HtmlDocument;
use Spwa\Html\Meta;
use Spwa\Html\MouseEvents;
use Spwa\Html\Script;
use Spwa\Html\Title;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Nodes\Page;

class WelcomePage extends Page
{
    public function __construct()
    {
        $this->state = new class {
            public string $text = "Vetty nice";
            public bool $active = false;
            public int $counter = 0;

            function inc(): void
            {
                $this->counter += 1;
            }
        };
    }

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
        return new Div(class: "h-screen w-screen flex", children: [
            new Div(class: "m-auto", children: [
                new Div(
                    mouse: new MouseEvents(onClick: fn() => $this->state->inc()),
                    children: [
                        new HtmlText("Counter: " . $this->state->counter),
                        new Div(children: [
                            new HtmlText("Inc"),
                        ]),
                    ])
            ])
        ]);
    }
}