<?php

namespace App\Components;

use Spwa\Template\Component;
use Spwa\Template\ComponentNode;
use Spwa\Template\ElementNode;
use function Spwa\Template\_class;
use function Spwa\Template\_for;
use function Spwa\Template\component;
use function Spwa\Template\div;
use function Spwa\Template\onClick;
use function Spwa\Template\text;

class TodoListProps
{
    public array $items;

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

}


/**
 * @extends Component<TodoListProps>
 */
class TodoList extends Component
{
    // helper function to create a componentNode
    static function create(array $items): ComponentNode
    {
        return component(self::class, new TodoListProps($items));
    }

    var $counter = 0;

    function dec(): void
    {
        $this->counter--;
    }

    function inc(): void
    {
        $this->counter++;
    }

    function view(): ElementNode
    {
        return div(
            div(
                onClick(fn() => $this->dec()),
                text("Counter: " . ($this->counter - 1))
            ),
            div(
                text("Counter: " . $this->counter)
            ),
            div(
                onClick(fn() => $this->inc()),
                text("Counter: " . ($this->counter + 1))
            ),
            _class("bg-blue-500 ml-6"),
            _for($this->props->items, fn($item) => div(
                text($item),
                _class("text-red-500")
            ))
        );
    }

}