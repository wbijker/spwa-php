<?php

namespace Spwa\UI;

/**
 * Inline flow layout - items wrap to next line when needed.
 * Inspired by QuestPDF Inlined.
 *
 * Usage:
 *   UI::inlined()
 *       ->spacing(Unit::small())
 *       ->alignLeft()
 *       ->alignMiddle()
 *       ->content(
 *           UI::badge("Tag 1"),
 *           UI::badge("Tag 2"),
 *           UI::badge("Tag 3")
 *       )
 */
class Inlined extends Container
{
    public function __construct()
    {
        $this->classes[] = 'flex';
        $this->classes[] = 'flex-wrap';
    }

    /**
     * Set spacing between items (both horizontal and vertical).
     */
    public function spacing(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('gap');
        }
        return $this;
    }

    /**
     * Set horizontal spacing.
     */
    public function spacingHorizontal(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('gap-x');
        }
        return $this;
    }

    /**
     * Set vertical spacing.
     */
    public function spacingVertical(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('gap-y');
        }
        return $this;
    }

    // ============================================================
    // Horizontal alignment (main axis) - explicit methods
    // ============================================================

    /**
     * Align items to left.
     */
    public function alignLeft(): static
    {
        $this->classes[] = 'justify-start';
        return $this;
    }

    /**
     * Align items to center.
     */
    public function alignCenter(): static
    {
        $this->classes[] = 'justify-center';
        return $this;
    }

    /**
     * Align items to right.
     */
    public function alignRight(): static
    {
        $this->classes[] = 'justify-end';
        return $this;
    }

    /**
     * Distribute items with space between.
     */
    public function alignBetween(): static
    {
        $this->classes[] = 'justify-between';
        return $this;
    }

    // ============================================================
    // Vertical alignment (cross axis) - explicit methods
    // ============================================================

    /**
     * Align items to top.
     */
    public function alignTop(): static
    {
        $this->classes[] = 'items-start';
        return $this;
    }

    /**
     * Align items to middle.
     */
    public function alignMiddle(): static
    {
        $this->classes[] = 'items-center';
        return $this;
    }

    /**
     * Align items to bottom.
     */
    public function alignBottom(): static
    {
        $this->classes[] = 'items-end';
        return $this;
    }

    /**
     * Align items to baseline.
     */
    public function alignBaseline(): static
    {
        $this->classes[] = 'items-baseline';
        return $this;
    }

    // ============================================================
    // Responsive alignment (overloaded methods)
    // ============================================================

    /**
     * Horizontal alignment with responsive/pseudo support.
     */
    public function align(Align ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('justify');
        }
        return $this;
    }

    /**
     * Vertical alignment with responsive/pseudo support.
     */
    public function alignVertical(Align ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('items');
        }
        return $this;
    }

    // ============================================================
    // Convenience methods
    // ============================================================

    /**
     * Center on both axes.
     */
    public function center(): static
    {
        $this->classes[] = 'justify-center';
        $this->classes[] = 'items-center';
        return $this;
    }
}
