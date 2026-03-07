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
class Row extends UIElement
{
    public function __construct()
    {
        parent::__construct('div');
        $this->addStyle('flex', ['display' => 'flex']);
        $this->addStyle('flex-row', ['flex-direction' => 'row']);
    }

    /**
     * Set gap between items.
     */
    public function gap(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('gap'), ['gap' => $value->getCssValue()]);
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
        $this->addStyle('justify-start', ['justify-content' => 'flex-start']);
        return $this;
    }

    /**
     * Align items to center.
     */
    public function alignCenter(): static
    {
        $this->addStyle('justify-center', ['justify-content' => 'center']);
        return $this;
    }

    /**
     * Align items to right.
     */
    public function alignRight(): static
    {
        $this->addStyle('justify-end', ['justify-content' => 'flex-end']);
        return $this;
    }

    /**
     * Distribute items with space between.
     */
    public function alignBetween(): static
    {
        $this->addStyle('justify-between', ['justify-content' => 'space-between']);
        return $this;
    }

    /**
     * Distribute items with space around.
     */
    public function alignAround(): static
    {
        $this->addStyle('justify-around', ['justify-content' => 'space-around']);
        return $this;
    }

    /**
     * Distribute items evenly.
     */
    public function alignEvenly(): static
    {
        $this->addStyle('justify-evenly', ['justify-content' => 'space-evenly']);
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
        $this->addStyle('items-start', ['align-items' => 'flex-start']);
        return $this;
    }

    /**
     * Align items to middle.
     */
    public function alignMiddle(): static
    {
        $this->addStyle('items-center', ['align-items' => 'center']);
        return $this;
    }

    /**
     * Align items to bottom.
     */
    public function alignBottom(): static
    {
        $this->addStyle('items-end', ['align-items' => 'flex-end']);
        return $this;
    }

    /**
     * Align items to baseline.
     */
    public function alignBaseline(): static
    {
        $this->addStyle('items-baseline', ['align-items' => 'baseline']);
        return $this;
    }

    /**
     * Stretch items vertically.
     */
    public function alignStretch(): static
    {
        $this->addStyle('items-stretch', ['align-items' => 'stretch']);
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
            $this->addStyle($value->withContext('justify'), ['justify-content' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Vertical alignment with responsive/pseudo support.
     */
    public function alignVertical(Align ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('items'), ['align-items' => $value->getCssValue()]);
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
        $this->addStyle('justify-center', ['justify-content' => 'center']);
        $this->addStyle('items-center', ['align-items' => 'center']);
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
        $this->addStyle('flex-wrap', ['flex-wrap' => 'wrap']);
        return $this;
    }

    /**
     * Prevent wrapping.
     */
    public function nowrap(): static
    {
        $this->addStyle('flex-nowrap', ['flex-wrap' => 'nowrap']);
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
            $this->addStyle($value->toClass(), ['flex-direction' => $value->getCssValue()]);
        }
        return $this;
    }
}
