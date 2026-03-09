<?php

namespace Spwa\UI;

/**
 * Preformatted text element.
 */
class Pre extends UIElement
{
    /** @var (UIElement|string)[] */
    protected array $children = [];

    public function __construct(?string $content = null)
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

    public function render(): DomNode
    {
        $node = $this->node('pre');

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
