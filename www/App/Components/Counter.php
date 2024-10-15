<?php

namespace App\Components;

use Spwa\Template\Component;
use Spwa\Template\ElementNode;
use function Spwa\Template\_class;
use function Spwa\Template\button;
use function Spwa\Template\div;
use function Spwa\Template\onClick;
use function Spwa\Template\text;

class CounterState
{
    public int $counter = 0;

    /**
     * @param int $counter
     */
    public function __construct(int $counter)
    {
        $this->counter = $counter;
    }
}

class Counter extends Component
{
    /**
     * @var
     */
    private $changeEvent;
    private int $eet;

    public function __construct(int $initial)
    {
        $this->state = new CounterState($initial);
    }

    function onChange(callable $callback): Counter
    {
        $this->changeEvent = $callback;
        return $this;
    }

    function view(): ElementNode
    {
        return div(
            div(
                _class("text-center"),
                text("Counter: " . $this->state->counter),
            ),
            div(
                button(
                    text("inc"),
                    _class("m-1 px-2 border shadow cursor-pointer"),

                    onClick(function () {
                        ($this->changeEvent)($this->state->counter);
                        $this->state->counter++;
                    })
                )
            ),
            div(
                button(
                    text("dec"),
                    _class("m-1 px-2 border shadow cursor-pointer"),
                    onClick(function () {
                        ($this->changeEvent)($this->state->counter);
                        $this->state->counter--;
                    })
                )
            )
        );
    }
}