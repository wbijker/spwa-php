<?php

namespace Spwa\UI;

/**
 * Noscript fallback element.
 */
class Noscript extends UIElement
{
    /** @var (UIElement|string)[] */
    protected array $children = [];

    public function content(UIElement|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('noscript');

        foreach ($this->children as $child) {
            if ($child instanceof UIElement) {
                $node->children($child->render());
            } else {
                $node->children($child);
            }
        }

        return $node;
    }
}
