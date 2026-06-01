<?php

namespace BrickPHP\UI;

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

    /** @var array<string, true> Attribute names that should force-patch on every diff */
    protected array $invalidatedAttrs = [];

    public function fill(string|Color $color): static
    {
        $this->fill = $color instanceof Color ? $color->getValue() : $color;
        return $this;
    }

    public function stroke(string|Color $color): static
    {
        $this->stroke = $color instanceof Color ? $color->getValue() : $color;
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

    public function transform(string $value, bool $invalidate = false): static
    {
        $this->transform = $value;
        if ($invalidate) {
            $this->invalidatedAttrs['transform'] = true;
        } else {
            unset($this->invalidatedAttrs['transform']);
        }
        return $this;
    }

    /**
     * Force-patch a single attribute on the rendered node regardless of how
     * it was set. Mirrors UIElement::invalidateAttr() for SVG children.
     */
    public function invalidateAttr(string $name): static
    {
        $this->invalidatedAttrs[$name] = true;
        return $this;
    }

    protected function applyCommonAttrs(DomNode $node): void
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

        if ($node instanceof TagDomNode) {
            foreach ($this->invalidatedAttrs as $name => $_) {
                $node->markInvalidatedAttr($name);
            }
        }
    }

    abstract public function toNode(): DomNode;
}
