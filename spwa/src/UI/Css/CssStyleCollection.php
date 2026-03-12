<?php

namespace Spwa\UI\Css;

/**
 * Collection of CssStyle objects with compression and output support.
 */
class CssStyleCollection
{
    /** @var array<string, CssStyle> Styles indexed by class name */
    private array $styles = [];

    /**
     * Add a style to the collection.
     */
    public function add(CssStyle $style): self
    {
        $this->styles[$style->getClassName()] = $style;
        return $this;
    }

    /**
     * Add multiple styles.
     * @param CssStyle[] $styles
     */
    public function addAll(array $styles): self
    {
        foreach ($styles as $style) {
            $this->add($style);
        }
        return $this;
    }

    /**
     * Create from array of CssStyle objects.
     * @param CssStyle[] $styles
     */
    public static function from(array $styles): self
    {
        $collection = new self();
        $collection->addAll($styles);
        return $collection;
    }

    /**
     * Compute delta (styles in this collection not in other).
     */
    public function delta(self $other): self
    {
        $delta = new self();
        foreach ($this->styles as $className => $style) {
            if (!isset($other->styles[$className])) {
                $delta->add($style);
            }
        }
        return $delta;
    }

    /**
     * Generate raw CSS string (minified).
     */
    public function toCss(): string
    {
        $css = '';
        foreach ($this->styles as $style) {
            $css .= $style->toCss();
        }
        return $css;
    }

    /**
     * Generate raw CSS for frontend (className => CSS rule).
     * @return array<string, string>
     */
    public function toRaw(): array
    {
        $raw = [];
        foreach ($this->styles as $className => $style) {
            $raw[$className] = $style->toCss();
        }
        return $raw;
    }

    /**
     * Generate compressed format for frontend transmission.
     * Format: { className: [breakpoint, colorScheme, [pseudos], prop1, val1, ...], ... }
     * @return array<string, array>
     */
    public function toCompressed(): array
    {
        $compressed = [];
        foreach ($this->styles as $className => $style) {
            $compressed[$className] = $style->toCompressed();
        }
        return $compressed;
    }

    /**
     * Get count of styles.
     */
    public function count(): int
    {
        return count($this->styles);
    }

    /**
     * Check if collection is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->styles);
    }

    /**
     * Get all style objects.
     * @return CssStyle[]
     */
    public function all(): array
    {
        return array_values($this->styles);
    }
}
