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
        $this->addStyle('flex', ['display' => 'flex']);
        $this->addStyle('flex-wrap', ['flex-wrap' => 'wrap']);
    }

    /**
     * Set spacing between items (both horizontal and vertical).
     */
    public function spacing(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('gap'), ['gap' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set horizontal spacing.
     */
    public function spacingHorizontal(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('gap-x'), ['column-gap' => $value->getCssValue()]);
        }
        return $this;
    }

    /**
     * Set vertical spacing.
     */
    public function spacingVertical(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStyle($value->withContext('gap-y'), ['row-gap' => $value->getCssValue()]);
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
}
