<?php

namespace Spwa\UI;

/**
 * Horizontal stack layout - items arranged left to right.
 * Inspired by QuestPDF Row.
 *
 * Usage:
 *   UI::row()
 *       ->gap(Unit::medium())
 *       ->alignCenter()
 *       ->alignMiddle()
 *       ->content(...)
 */
class Row extends Container
{
    public function __construct()
    {
        $this->classes[] = 'flex';
        $this->classes[] = 'flex-row';
    }

    /**
     * Set gap between items.
     */
    public function gap(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->withContext('gap');
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

    /**
     * Distribute items with space around.
     */
    public function alignAround(): static
    {
        $this->classes[] = 'justify-around';
        return $this;
    }

    /**
     * Distribute items evenly.
     */
    public function alignEvenly(): static
    {
        $this->classes[] = 'justify-evenly';
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

    /**
     * Stretch items vertically.
     */
    public function alignStretch(): static
    {
        $this->classes[] = 'items-stretch';
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

    // ============================================================
    // Wrapping
    // ============================================================

    /**
     * Allow items to wrap to next line.
     */
    public function wrap(): static
    {
        $this->classes[] = 'flex-wrap';
        return $this;
    }

    /**
     * Prevent wrapping.
     */
    public function nowrap(): static
    {
        $this->classes[] = 'flex-nowrap';
        return $this;
    }

    // ============================================================
    // Responsive direction change
    // ============================================================

    /**
     * Change direction with responsive/pseudo support.
     */
    public function direction(Direction ...$values): static
    {
        foreach ($values as $value) {
            $this->classes[] = $value->toClass();
        }
        return $this;
    }
}
