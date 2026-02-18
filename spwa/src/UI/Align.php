<?php

namespace Spwa\UI;

/**
 * Horizontal alignment (justify-content in flex).
 */
enum AlignX: string
{
    case Start = 'start';
    case Center = 'center';
    case End = 'end';
    case Between = 'between';
    case Around = 'around';
    case Evenly = 'evenly';
    case Stretch = 'stretch';
}

/**
 * Vertical alignment (align-items in flex).
 */
enum AlignY: string
{
    case Start = 'start';
    case Center = 'center';
    case End = 'end';
    case Baseline = 'baseline';
    case Stretch = 'stretch';
}

/**
 * Stateful horizontal alignment value.
 */
class AlignXValue extends StateValue
{
    public function __construct(
        protected AlignX $align
    ) {
    }

    protected function getBaseClass(): string
    {
        return 'justify-' . $this->align->value;
    }

    public static function start(): static
    {
        return new static(AlignX::Start);
    }

    public static function left(): static
    {
        return new static(AlignX::Start);
    }

    public static function center(): static
    {
        return new static(AlignX::Center);
    }

    public static function end(): static
    {
        return new static(AlignX::End);
    }

    public static function right(): static
    {
        return new static(AlignX::End);
    }

    public static function between(): static
    {
        return new static(AlignX::Between);
    }

    public static function around(): static
    {
        return new static(AlignX::Around);
    }

    public static function evenly(): static
    {
        return new static(AlignX::Evenly);
    }

    public static function stretch(): static
    {
        return new static(AlignX::Stretch);
    }
}

/**
 * Stateful vertical alignment value.
 */
class AlignYValue extends StateValue
{
    public function __construct(
        protected AlignY $align
    ) {
    }

    protected function getBaseClass(): string
    {
        return 'items-' . $this->align->value;
    }

    public static function start(): static
    {
        return new static(AlignY::Start);
    }

    public static function top(): static
    {
        return new static(AlignY::Start);
    }

    public static function center(): static
    {
        return new static(AlignY::Center);
    }

    public static function middle(): static
    {
        return new static(AlignY::Center);
    }

    public static function end(): static
    {
        return new static(AlignY::End);
    }

    public static function bottom(): static
    {
        return new static(AlignY::End);
    }

    public static function baseline(): static
    {
        return new static(AlignY::Baseline);
    }

    public static function stretch(): static
    {
        return new static(AlignY::Stretch);
    }
}

/**
 * Self alignment (align-self).
 */
class AlignSelf extends StateValue
{
    public function __construct(
        protected string $value
    ) {
    }

    protected function getBaseClass(): string
    {
        return 'self-' . $this->value;
    }

    public static function auto(): static
    {
        return new static('auto');
    }

    public static function start(): static
    {
        return new static('start');
    }

    public static function center(): static
    {
        return new static('center');
    }

    public static function end(): static
    {
        return new static('end');
    }

    public static function stretch(): static
    {
        return new static('stretch');
    }

    public static function baseline(): static
    {
        return new static('baseline');
    }
}
