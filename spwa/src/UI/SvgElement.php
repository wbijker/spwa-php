<?php

namespace Spwa\UI;

/**
 * Base class for SVG child elements.
 */
abstract class SvgElement
{
    protected ?string $fill = null;
    protected ?string $stroke = null;
    protected ?string $strokeWidth = null;
    protected ?string $strokeLinecap = null;
    protected ?string $strokeLinejoin = null;
    protected ?string $opacity = null;
    protected ?string $transform = null;

    public function fill(string $color): static
    {
        $this->fill = $color;
        return $this;
    }

    public function stroke(string $color): static
    {
        $this->stroke = $color;
        return $this;
    }

    public function strokeWidth(string $width): static
    {
        $this->strokeWidth = $width;
        return $this;
    }

    public function strokeLinecap(string $cap): static
    {
        $this->strokeLinecap = $cap;
        return $this;
    }

    public function strokeLinejoin(string $join): static
    {
        $this->strokeLinejoin = $join;
        return $this;
    }

    public function opacity(string $value): static
    {
        $this->opacity = $value;
        return $this;
    }

    public function transform(string $value): static
    {
        $this->transform = $value;
        return $this;
    }

    protected function applyCommonAttrs(Node $node): void
    {
        if ($this->fill !== null) {
            $node->attr('fill', $this->fill);
        }
        if ($this->stroke !== null) {
            $node->attr('stroke', $this->stroke);
        }
        if ($this->strokeWidth !== null) {
            $node->attr('stroke-width', $this->strokeWidth);
        }
        if ($this->strokeLinecap !== null) {
            $node->attr('stroke-linecap', $this->strokeLinecap);
        }
        if ($this->strokeLinejoin !== null) {
            $node->attr('stroke-linejoin', $this->strokeLinejoin);
        }
        if ($this->opacity !== null) {
            $node->attr('opacity', $this->opacity);
        }
        if ($this->transform !== null) {
            $node->attr('transform', $this->transform);
        }
    }

    abstract public function toNode(): Node;
}
