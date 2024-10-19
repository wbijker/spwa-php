<?php

namespace App\Components;

use Spwa\Template\Component;
use Spwa\Template\ElementNode;
use function Spwa\Template\_class;
use function Spwa\Template\attr;
use function Spwa\Template\button;
use function Spwa\Template\div;
use function Spwa\Template\span;
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
     * @var callable
     */
    private $changeEvent;

    /**
     * @param callable(int): void $callback
     * @return Counter
     */
    function onChange(callable $callback): Counter
    {
        $this->changeEvent = $callback;
        return $this;
    }

    public function __construct(int $initial)
    {
        $this->state = new CounterState($initial);
    }

    function clicked($inc): \Closure
    {
        return function () use ($inc) {
            $this->emit($this->changeEvent, $this->state->counter);
            $this->state->counter += $inc;
        };
    }

    function view(): ElementNode
    {
        return div(
            div(
                _class("text-center bg-gray-200 p-4"),

                div(
                    _class("text-2xl"),
                    div(
                        attr("x-data", json_encode(["count" => $this->state->counter])),
                        button(
                            // handleInput($event, ++count)
                            attr("x-on:click", "setValue(\$event, 'counter', ++count)"),
                            "Inc"
                        ),
                        span(
                            attr("x-text", "count"),
                        )
                    )
                ),
            ),

            div(
                _class("text-center"),
                "Counter: " . $this->state->counter,
            ),
            div(
                button(
                    text("inc"),
                    _class("m-1 px-2 border shadow cursor-pointer"),
                )
                    ->onClick($this->clicked(1))
            ),
            div(
                button(
                    text("dec"),
                    _class("m-1 px-2 border shadow cursor-pointer"),
                )
                    ->onClick($this->clicked(-1))
            )
        );
    }
}