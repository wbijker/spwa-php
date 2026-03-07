<?php

namespace Spwa\UI;

/**
 * Fieldset element.
 */
class Fieldset extends Container
{
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

    public function render(): Node
    {
        $node = $this->node('fieldset');

        if ($this->disabled) {
            $node->attr('disabled', 'disabled');
        }

        if ($this->name !== null) {
            $node->attr('name', $this->name);
        }

        if ($this->legend !== null) {
            $node->children(Node::el('legend')->children($this->legend));
        }

        foreach ($this->children as $child) {
            $node->children($child->render());
        }

        return $node;
    }
}
