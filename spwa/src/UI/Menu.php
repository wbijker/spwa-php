<?php

namespace Spwa\UI;

/**
 * Menu element (semantic list of commands).
 */
class Menu extends UIElement
{
    /** @var Li[] */
    protected array $items = [];

    public function items(Li ...$items): static
    {
        $this->items = array_merge($this->items, $items);
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('menu');

        foreach ($this->items as $item) {
            $node->children($item->build());
        }

        return $node;
    }
}
