<?php

namespace App\Components;

use Spwa\Html\Div;
use Spwa\Html\MouseEvents;
use Spwa\Js\JS;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Nodes\RenderContext;

class CounterState
{
    public int $counter = 1;

    public function inc()
    {
        $this->counter += 1;
    }
}

class Counter extends Component
{

    private CounterState $state;

    public function __construct()
    {
        $this->state = $this->createState(new CounterState());
    }

    function render(): Node
    {
        return new Div(
            mouse: new MouseEvents(onClick: fn() => $this->state->inc()),
            children: [
                new HtmlText("Counter: " . $this->state->counter),
                new Div(children: [
                    new HtmlText("Inc"),
                ]),
            ]);
    }
}