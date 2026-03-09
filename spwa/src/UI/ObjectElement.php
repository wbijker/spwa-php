<?php

namespace Spwa\UI;

/**
 * Object element for external resources.
 */
class ObjectElement extends UIElement
{
    protected ?string $data = null;
    protected ?string $type = null;
    protected ?string $name = null;
    /** @var UIElement[] */
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

    public function content(UIElement ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function render(): DomNode
    {
        $node = $this->node('object');

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
            $node->children($child->render());
        }

        return $node;
    }
}
