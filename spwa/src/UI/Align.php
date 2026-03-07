<?php

namespace Spwa\UI;

/**
 * Alignment value - pure value class.
 * Context (justify/items) is determined by usage in layout components.
 *
 * Usage:
 *   Align::left()
 *   Align::center()
 *   Align::middle()
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
     * Build class with context (justify, items, etc).
     */
    public function withContext(string $context): string
    {
        return $this->prefix() . $context . '-' . $this->value->value;
    }

    /**
     * Get the CSS value for this alignment.
     */
    public function getCssValue(): string
    {
        return match ($this->value) {
            AlignValue::Start => 'flex-start',
            AlignValue::Center => 'center',
            AlignValue::End => 'flex-end',
            AlignValue::Between => 'space-between',
            AlignValue::Around => 'space-around',
            AlignValue::Evenly => 'space-evenly',
            AlignValue::Stretch => 'stretch',
            AlignValue::Baseline => 'baseline',
        };
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
