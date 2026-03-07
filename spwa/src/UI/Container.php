<?php

namespace Spwa\UI;

/**
 * Basic container element that can hold children.
 */
class Container extends UIElement
{
    /** @var UIElement[] */
    protected array $children = [];

    /**
     * Add child elements.
     */
    public function content(UIElement ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    /**
     * Get child elements.
     * @return UIElement[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function render(): Node
    {
        $node = $this->node('div');

        foreach ($this->children as $child) {
            $node->children($child->render());
        }

        return $node;
    }
}
