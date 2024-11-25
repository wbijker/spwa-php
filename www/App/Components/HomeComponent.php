<?php

namespace App\Components;

use Spwa\Html\MouseEvents;
use Spwa\Nodes\Component;
use Spwa\Nodes\ForNode;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Html\Div;
use function Spwa\Html\div;

class HomeComponent extends Component
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
    
    function render(): Node
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