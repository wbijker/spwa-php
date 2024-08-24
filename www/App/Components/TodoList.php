<?php

namespace App\Components;

use Spwa\Template\Component;
use Spwa\Template\ElementNode;
use function Spwa\Template\_class;
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


    function view(): ElementNode
    {
        return div(
            _class("bg-blue-500 ml-6"),
            ...array_map(fn($item) => div(
                text($item),
                _class("text-red-500")
            ), $this->items)

        );

    }
}