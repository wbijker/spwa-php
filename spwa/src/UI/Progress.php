<?php

namespace Spwa\UI;

/**
 * Progress element.
 */
class Progress extends UIElement
{
    protected ?float $value = null;
    protected ?float $max = null;

    public function value(float $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function max(float $max): static
    {
        $this->max = $max;
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('progress');

        if ($this->value !== null) {
            $node->attr('value', (string)$this->value);
        }

        if ($this->max !== null) {
            $node->attr('max', (string)$this->max);
        }

        return $node;
    }
}
