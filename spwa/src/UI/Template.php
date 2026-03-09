<?php

namespace Spwa\UI;

/**
 * Template element (not rendered, for JS use).
 */
class Template extends UIElement
{
    protected ?string $id = null;
    /** @var UIElement[] */
    protected array $children = [];

    public function id(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function content(UIElement ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function render(): DomNode
    {
        $node = $this->node('template');

        if ($this->id !== null) {
            $node->attr('id', $this->id);
        }

        foreach ($this->children as $child) {
            $node->children($child->render());
        }

        return $node;
    }
}
