<?php

namespace Spwa\UI;

/**
 * Line elements for creating horizontal and vertical dividers.
 * Similar to QuestPDF's LineHorizontal and LineVertical.
 *
 * Usage:
 *   UI::lineHorizontal()->color(Color::gray(300))
 *   UI::lineVertical()->thickness(Unit::px(2))
 */
class LineElement extends BaseStyledElement
{
    protected bool $isVertical;
    protected string $thickness = '1';

    public function __construct(bool $vertical = false)
    {
        $this->isVertical = $vertical;

        if ($vertical) {
            $this->addClass('w-px');
            $this->addClass('h-full');
        } else {
            $this->addClass('h-px');
            $this->addClass('w-full');
        }
    }

    /**
     * Set line thickness.
     */
    public function thickness(int $px): static
    {
        // Remove default thickness
        if ($this->isVertical) {
            $this->classes = array_filter($this->classes, fn($c) => !str_starts_with($c, 'w-'));
            $this->addClass('w-[' . $px . 'px]');
        } else {
            $this->classes = array_filter($this->classes, fn($c) => !str_starts_with($c, 'h-'));
            $this->addClass('h-[' . $px . 'px]');
        }
        return $this;
    }

    /**
     * Set line color.
     */
    public function lineColor(Color $color): static
    {
        $this->addStateValue($color);
        return $this;
    }

    /**
     * Make line dashed.
     */
    public function dashed(): static
    {
        if ($this->isVertical) {
            $this->addClass('border-l');
            $this->addClass('border-dashed');
            $this->classes = array_filter($this->classes, fn($c) => $c !== 'w-px' && !str_starts_with($c, 'w-['));
            $this->addClass('w-0');
        } else {
            $this->addClass('border-t');
            $this->addClass('border-dashed');
            $this->classes = array_filter($this->classes, fn($c) => $c !== 'h-px' && !str_starts_with($c, 'h-['));
            $this->addClass('h-0');
        }
        return $this;
    }

    /**
     * Make line dotted.
     */
    public function dotted(): static
    {
        if ($this->isVertical) {
            $this->addClass('border-l');
            $this->addClass('border-dotted');
            $this->classes = array_filter($this->classes, fn($c) => $c !== 'w-px' && !str_starts_with($c, 'w-['));
            $this->addClass('w-0');
        } else {
            $this->addClass('border-t');
            $this->addClass('border-dotted');
            $this->classes = array_filter($this->classes, fn($c) => $c !== 'h-px' && !str_starts_with($c, 'h-['));
            $this->addClass('h-0');
        }
        return $this;
    }

    public function render(): void
    {
        $classAttr = $this->buildClassAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        echo "<div{$classHtml}></div>";
    }
}
