<?php

namespace Spwa\UI;

/**
 * Meter element.
 */
class Meter extends UIElement
{
    protected ?float $value = null;
    protected ?float $min = null;
    protected ?float $max = null;
    protected ?float $low = null;
    protected ?float $high = null;
    protected ?float $optimum = null;

    public function value(float $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function min(float $min): static
    {
        $this->min = $min;
        return $this;
    }

    public function max(float $max): static
    {
        $this->max = $max;
        return $this;
    }

    public function low(float $low): static
    {
        $this->low = $low;
        return $this;
    }

    public function high(float $high): static
    {
        $this->high = $high;
        return $this;
    }

    public function optimum(float $optimum): static
    {
        $this->optimum = $optimum;
        return $this;
    }

    public function render(): DomNode
    {
        $node = $this->node('meter');

        if ($this->value !== null) {
            $node->attr('value', (string)$this->value);
        }

        if ($this->min !== null) {
            $node->attr('min', (string)$this->min);
        }

        if ($this->max !== null) {
            $node->attr('max', (string)$this->max);
        }

        if ($this->low !== null) {
            $node->attr('low', (string)$this->low);
        }

        if ($this->high !== null) {
            $node->attr('high', (string)$this->high);
        }

        if ($this->optimum !== null) {
            $node->attr('optimum', (string)$this->optimum);
        }

        return $node;
    }
}
