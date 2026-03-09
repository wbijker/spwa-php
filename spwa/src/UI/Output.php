<?php

namespace Spwa\UI;

/**
 * Output element.
 */
class Output extends UIElement
{
    protected ?string $name = null;
    protected ?string $for = null;
    protected ?string $content = null;

    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function for(string $for): static
    {
        $this->for = $for;
        return $this;
    }

    public function content(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function render(): DomNode
    {
        $node = $this->node('output');

        if ($this->name !== null) {
            $node->attr('name', $this->name);
        }

        if ($this->for !== null) {
            $node->attr('for', $this->for);
        }

        if ($this->content !== null) {
            $node->children($this->content);
        }

        return $node;
    }
}
