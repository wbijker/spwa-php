<?php

namespace Spwa\Js;

class JsArray extends JsVar
{
    /**
     * @var JsVar[]
     */
    public array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    function dump(): string
    {
        return "[" . implode(", ", array_map(fn($item) => $item->dump(), $this->items)) . "]";
    }
}