<?php

namespace App\Components;

use Spwa\Html\Div;
use Spwa\Html\HtmlDocument;
use Spwa\Html\InputText;
use Spwa\Html\Meta;
use Spwa\Html\Script;
use Spwa\Html\Title;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Nodes\RenderContext;
use Spwa\Nodes\State;

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

    function body(): Node
    {
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

            new InputText(class: "p-2 border", bind: $this->text),

            new Div(children: array_map(fn($i) => new Div(key: "#" . $i, children: [
                new HtmlText("Item $i render"),
                new Counter(onChange: function ($value) use ($other) {
                    $this->last?->setCounter($value);
                    $other->setCounter($value);
                }),
            ]), [1, 2, 3, 4, 5])),

            new Div(class: "last", children: [
                new HtmlText("Last "),
            ]),

            new Counter(ref: function ($instance) {
                $this->last = $instance;
            }),
            $other
        ]);
    }
}