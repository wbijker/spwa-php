<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

/**
 * List item element.
 */
class Li extends UIElement
{
    /** @var (DomNode|VNode|string)[] */
    protected array $children = [];
    protected ?int $value = null;

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

    public function value(int $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('li');

        if ($this->value !== null) {
            $node->attr('value', (string)$this->value);
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
