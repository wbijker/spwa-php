<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

/**
 * Description details element.
 */
class Dd extends UIElement
{
    /** @var (DomNode|VNode|string)[] */
    protected array $children = [];

    public function __construct(string|UIElement|null $content = null)
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
        $node = $this->dom()->setTag('dd');

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
