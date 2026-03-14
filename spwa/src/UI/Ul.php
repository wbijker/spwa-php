<?php

namespace Spwa\UI;

/**
 * Unordered list element.
 */
class Ul extends UIElement
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
        $node = $this->dom()->setTag('ul');

        foreach ($this->items as $item) {
            $node->children($item->build());
        }

        return $node;
    }
}
