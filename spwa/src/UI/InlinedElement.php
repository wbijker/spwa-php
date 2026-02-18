<?php

namespace Spwa\UI;

/**
 * Inlined element for flow layout (items wrap to next line).
 * Similar to QuestPDF's Inlined element.
 *
 * Usage:
 *   UI::inlined()
 *       ->spacing(Unit::sizeSm())
 *       ->alignCenter()
 *       ->baselineMiddle()
 *       ->children(...)
 */
class InlinedElement extends Element
{
    public function __construct()
    {
        parent::__construct('div');
        $this->addClass('flex');
        $this->addClass('flex-wrap');
    }

    /**
     * Set both horizontal and vertical spacing.
     */
    public function spacing(Unit $value): static
    {
        $this->addStateValue($value->asGap());
        return $this;
    }

    /**
     * Set horizontal spacing between items.
     */
    public function horizontalSpacing(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asGapX());
        }
        return $this;
    }

    /**
     * Set vertical spacing between lines.
     */
    public function verticalSpacing(Unit ...$values): static
    {
        foreach ($values as $value) {
            $this->addStateValue($value->asGapY());
        }
        return $this;
    }

    // Horizontal alignment (with responsive support)
    public function alignLeft(AlignXValue ...$responsive): static
    {
        $this->addStateValue(AlignXValue::start());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    public function alignCenter(AlignXValue ...$responsive): static
    {
        $this->addStateValue(AlignXValue::center());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    public function alignRight(AlignXValue ...$responsive): static
    {
        $this->addStateValue(AlignXValue::end());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    public function alignJustify(AlignXValue ...$responsive): static
    {
        $this->addStateValue(AlignXValue::between());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    public function alignSpaceAround(AlignXValue ...$responsive): static
    {
        $this->addStateValue(AlignXValue::around());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    public function alignSpaceEvenly(AlignXValue ...$responsive): static
    {
        $this->addStateValue(AlignXValue::evenly());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    // Baseline/vertical alignment (with responsive support)
    public function baselineTop(AlignYValue ...$responsive): static
    {
        $this->addStateValue(AlignYValue::start());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    public function baselineMiddle(AlignYValue ...$responsive): static
    {
        $this->addStateValue(AlignYValue::center());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    public function baselineBottom(AlignYValue ...$responsive): static
    {
        $this->addStateValue(AlignYValue::end());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    public function baselineBaseline(AlignYValue ...$responsive): static
    {
        $this->addStateValue(AlignYValue::baseline());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }

    public function baselineStretch(AlignYValue ...$responsive): static
    {
        $this->addStateValue(AlignYValue::stretch());
        foreach ($responsive as $value) {
            $this->addStateValue($value);
        }
        return $this;
    }
}
