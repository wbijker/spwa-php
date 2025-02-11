<?php

namespace App\Components;

use Spwa\Html\Div;
use Spwa\Html\MouseEvents;
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
    function render(RenderContext $context): Node
    {
        $state = $context->createState(new CounterState());

        return new Div(
            mouse: new MouseEvents(onClick: fn() => $state->inc()),
            children: [
                new HtmlText("Counter: " . $state->counter),
                new Div(children: [
                    new HtmlText("Inc"),
                ]),
            ]);
    }
}