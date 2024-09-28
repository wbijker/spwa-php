<?php

namespace App\Components;

use Spwa\Js\JS;
use Spwa\Template\Component;
use Spwa\Template\ComponentNode;
use Spwa\Template\ElementNode;
use function Spwa\Template\_class;
use function Spwa\Template\button;
use function Spwa\Template\div;
use function Spwa\Template\onClick;
use function Spwa\Template\text;


/**
 * Class Counter
 * @extends  Component<(int) => void>
 */
class Counter extends Component
{
    var int $counter = 0;
    var string $name = "Counter 2";

    public function __construct()
    {
        JS::log("Counter constructor called");
    }

    static function component(callable $onChange): ComponentNode
    {
        return new ComponentNode(
            self::class,
            [
                "onChange" => $onChange
            ]);
    }

    /**
     * @param callable $props
     * @return ComponentNode
     */

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
                    onClick(function () {
                        ($this->props['onChange'])($this->counter);
                        $this->counter++;
                    })
                )
            ),
            div(
                button(
                    text("dec"),
                    _class("m-1 px-2 border shadow cursor-pointer"),
                    onClick(function () {
                        ($this->props['onChange'])($this->counter);
                        $this->counter--;
                    })
                )
            )
        );
    }

    public function onChange(\Closure $param): Counter
    {
        $this->props['onChange'] = $param;
        return $this;
    }
}