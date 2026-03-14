<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

/**
 * Label element.
 */
class Label extends UIElement
{
    protected ?string $for = null;
    /** @var (DomNode|VNode|string)[] */
    protected array $children = [];

    public function __construct(?string $text = null)
    {
        if ($text !== null) {
            $this->children[] = $text;
        }
    }

    public function for(string $id): static
    {
        $this->for = $id;
        return $this;
    }

    public function content(DomNode|VNode|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('label');

        if ($this->for !== null) {
            $node->attr('for', $this->for);
        }

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
