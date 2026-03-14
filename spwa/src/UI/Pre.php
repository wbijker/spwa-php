<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

/**
 * Preformatted text element.
 */
class Pre extends UIElement
{
    /** @var (DomNode|VNode|string)[] */
    protected array $children = [];

    public function __construct(?string $content = null)
    {
        if ($content !== null) {
            $this->children[] = $content;
        }
    }

    public function content(DomNode|VNode|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('pre');

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
