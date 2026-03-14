<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

/**
 * Noscript fallback element.
 */
class Noscript extends UIElement
{
    /** @var (DomNode|VNode|string)[] */
    protected array $children = [];

    public function content(DomNode|VNode|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('noscript');

        foreach ($this->children as $child) {
            if ($child instanceof UIElement) {
                $node->children($child->build());
            } else {
                $node->children($child);
            }
        }

        return $node;
    }
}
