<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

/**
 * Output element.
 */
class Output extends UIElement
{
    public function __construct()
    {
        parent::__construct('output');
    }

    protected ?string $name = null;
    protected ?string $for = null;
    protected DomNode|VNode|string|null $content = null;

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

    public function content(DomNode|VNode|string|null ...$children): static
    {
        $this->content = $children[0] ?? null;
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('output');

        if ($this->name !== null) {
            $node->attr('name', $this->name);
        }

        if ($this->for !== null) {
            $node->attr('for', $this->for);
        }

        if ($this->content instanceof UIElement) {
            $node->children($this->content->build());
        } elseif ($this->content instanceof DomNode) {
            $node->children($this->content);
        } elseif (is_string($this->content)) {
            $node->children($this->content);
        }

        return $node;
    }
}
