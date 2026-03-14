<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

/**
 * Object element for external resources.
 */
class ObjectElement extends UIElement
{
    protected ?string $data = null;
    protected ?string $type = null;
    protected ?string $name = null;
    /** @var (DomNode|VNode|string)[] */
    protected array $children = [];

    public function data(string $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function content(DomNode|VNode|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('object');

        if ($this->data !== null) {
            $node->attr('data', $this->data);
        }

        if ($this->type !== null) {
            $node->attr('type', $this->type);
        }

        if ($this->name !== null) {
            $node->attr('name', $this->name);
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
