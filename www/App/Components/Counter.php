<?php

namespace App\Components;

use Spwa\Html\Div;
use Spwa\Html\MouseEvents;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Nodes\State;


class Counter extends Component
{
    #[State]
    public int $counter = 1;

    public function inc(): void
    {
        $this->counter += 1;
    }

    function render(): Node
    {
        return new Div(
            mouse: new MouseEvents(onClick: fn() => $this->inc()),
            children: [
                new HtmlText("Counter: " . $this->counter),
                new Div(children: [
                    new HtmlText("Inc"),
                ]),
            ]);
    }
}