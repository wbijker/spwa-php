<?php

namespace BrickPHP\UI;

/**
 * Fieldset element.
 */
class Fieldset extends Container
{
    public function __construct()
    {
        parent::__construct('fieldset');
    }

    protected ?string $legend = null;
    protected bool $disabled = false;
    protected ?string $name = null;

    public function legend(string $legend): static
    {
        $this->legend = $legend;
        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('fieldset');

        if ($this->disabled) {
            $node->attr('disabled', 'disabled');
        }

        if ($this->name !== null) {
            $node->attr('name', $this->name);
        }

        if ($this->legend !== null) {
            $node->children(DomNode::el('legend')->children($this->legend));
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
