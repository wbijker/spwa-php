<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

/**
 * Template element (not rendered, for JS use).
 */
class Template extends UIElement
{
    protected ?string $id = null;
    /** @var (DomNode|VNode|string)[] */
    protected array $children = [];

    public function id(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function content(DomNode|VNode|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('template');

        if ($this->id !== null) {
            $node->attr('id', $this->id);
        }

        foreach ($this->children as $child) {
            if ($child instanceof UIElement) {
                $node->children($child->build());
            } elseif ($child instanceof DomNode) {
                $node->children($child);
            } elseif (is_string($child)) {
                $node->children($child);
            }
        }

        return $node;
    }
}
