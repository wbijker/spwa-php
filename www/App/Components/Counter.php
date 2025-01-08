<?php

namespace App\Components;

use Spwa\Html\Div;
use Spwa\Html\MouseEvents;
use Spwa\Js\JS;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;


class Counter extends Component
{
    public int $counter = 0;

    function inc(): void
    {
        $this->counter += 1;
    }

    public function restoreState(array $saved): void
    {
        $this->counter = 10;
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