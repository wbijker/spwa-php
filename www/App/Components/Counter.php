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

    /**
     * @param callable(int $value): void $onChange
     * @param callable(Counter $instance): void $ref
     */
    public function __construct(private $onChange = null, $ref = null)
    {
        if (is_callable($ref)) {
            ($ref)($this);
        }
    }

    public function setCounter(int $value): void
    {
        $this->counter = $value;
    }

    private function inc(): void
    {
        $this->counter += 1;
        if (is_callable($this->onChange)) {
            ($this->onChange)($this->counter);
        }
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