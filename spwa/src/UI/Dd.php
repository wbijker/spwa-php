<?php

namespace Spwa\UI;

/**
 * Description details element.
 */
class Dd extends UIElement
{
    /** @var (UIElement|string)[] */
    protected array $children = [];

    public function __construct(string|UIElement|null $content = null)
    {
        if ($content !== null) {
            $this->children[] = $content;
        }
    }

    public function content(UIElement|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('dd');

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
