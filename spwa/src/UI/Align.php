<?php

namespace Spwa\UI;

/**
 * Unified alignment class for both horizontal and vertical alignment.
 *
 * Usage:
 *   Align::left()      // justify-start / items-start
 *   Align::center()    // justify-center / items-center
 *   Align::right()     // justify-end / items-end
 *   Align::top()       // justify-start / items-start
 *   Align::middle()    // justify-center / items-center
 *   Align::bottom()    // justify-end / items-end
 *   Align::between()   // justify-between
 *   Align::around()    // justify-around
 *   Align::evenly()    // justify-evenly
 *   Align::stretch()   // items-stretch
 *   Align::baseline()  // items-baseline
 *
 * With responsive/pseudo:
 *   Align::center()->md()
 *   Align::middle()->hover()
 */
class Align extends Property
{
    public function __construct(
        protected AlignValue $value
    ) {
    }

    protected function base(): string
    {
        return $this->value->value;
    }

    /**
     * Build class with justify context (for main axis).
     */
    public function asJustify(): string
    {
        return $this->prefix() . 'justify-' . $this->value->value;
    }

    /**
     * Build class with items context (for cross axis).
     */
    public function asItems(): string
    {
        return $this->prefix() . 'items-' . $this->value->value;
    }

    // ============================================================
    // Horizontal alignment (semantic names)
    // ============================================================

    public static function left(): static
    {
        return new static(AlignValue::Start);
    }

    public static function center(): static
    {
        return new static(AlignValue::Center);
    }

    public static function right(): static
    {
        return new static(AlignValue::End);
    }

    // ============================================================
    // Vertical alignment (semantic names)
    // ============================================================

    public static function top(): static
    {
        return new static(AlignValue::Start);
    }

    public static function middle(): static
    {
        return new static(AlignValue::Center);
    }

    public static function bottom(): static
    {
        return new static(AlignValue::End);
    }

    // ============================================================
    // Generic alignment (CSS names)
    // ============================================================

    public static function start(): static
    {
        return new static(AlignValue::Start);
    }

    public static function end(): static
    {
        return new static(AlignValue::End);
    }

    // ============================================================
    // Distribution
    // ============================================================

    public static function between(): static
    {
        return new static(AlignValue::Between);
    }

    public static function around(): static
    {
        return new static(AlignValue::Around);
    }

    public static function evenly(): static
    {
        return new static(AlignValue::Evenly);
    }

    // ============================================================
    // Cross-axis specific
    // ============================================================

    public static function stretch(): static
    {
        return new static(AlignValue::Stretch);
    }

    public static function baseline(): static
    {
        return new static(AlignValue::Baseline);
    }
}

enum AlignValue: string
{
    case Start = 'start';
    case Center = 'center';
    case End = 'end';
    case Between = 'between';
    case Around = 'around';
    case Evenly = 'evenly';
    case Stretch = 'stretch';
    case Baseline = 'baseline';
}
