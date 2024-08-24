<?php

namespace App\Components;

use Spwa\Template\Component;
use Spwa\Template\ElementNode;
use function Spwa\Template\_class;
use function Spwa\Template\_for;
use function Spwa\Template\div;
use function Spwa\Template\text;

class TodoList extends Component
{
    /**
     * @var array
     */
    public $items = [];

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    var $counter = 0;

    function view(): ElementNode
    {
        return div(
            div(
                text("Counter: ".($this->counter - 1))
            ),
            div(
                text("Counter: ".$this->counter)
            ),
            div(
                text("Counter: ".($this->counter + 1))
            ),
            _class("bg-blue-500 ml-6"),
            _for($this->items, fn($item) => div(
                text($item),
                _class("text-red-500")
            ))
        );
    }
}