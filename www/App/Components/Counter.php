<?php

namespace App\Components;

use Spwa\Template\Component;
use Spwa\Template\ElementNode;
use function Spwa\Template\_class;
use function Spwa\Template\button;
use function Spwa\Template\div;
use function Spwa\Template\onClick;
use function Spwa\Template\text;

class Counter extends Component
{
    var int $counter = 0;
    var string $name = "Counter 2";

    public function __construct()
    {
    }

    function view(): ElementNode
    {
        return div(
            div(
                _class("text-center"),
                text("Counter: " . $this->counter),
            ),
            div(
                button(
                    text("inc"),
                    _class("m-1 px-2 border shadow cursor-pointer"),
                    onClick(fn() => $this->counter++)
                )
            ),
            div(
                button(
                    text("dec"),
                    _class("m-1 px-2 border shadow cursor-pointer"),
                    onClick(fn() => $this->counter--)
                )
            )
        );
    }

//    public function serialize(): string
//    {
//        return serialize($this->counter);
//    }
//
//    public function unserialize($data)
//    {
//        $this->counter = unserialize($data);
//    }
}