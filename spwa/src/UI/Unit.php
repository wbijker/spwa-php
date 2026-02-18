<?php

namespace Spwa\UI;

/**
 * Stateful unit value for spacing, sizing, etc.
 *
 * Usage:
 *   Unit::px(4)              // 4 (Tailwind scale)
 *   Unit::full()->hover()    // hover:w-full (depends on context)
 *   Unit::rem(2)->sm()       // sm:... (2rem)
 */
class Unit extends StateValue
{
    protected string $prefix = '';

    public function __construct(
        protected string $value
    ) {
    }

    /**
     * Set this unit for width context.
     */
    public function asWidth(): static
    {
        return $this->with(fn($s) => $s->prefix = 'w');
    }

    /**
     * Set this unit for height context.
     */
    public function asHeight(): static
    {
        return $this->with(fn($s) => $s->prefix = 'h');
    }

    /**
     * Set this unit for min-width context.
     */
    public function asMinWidth(): static
    {
        return $this->with(fn($s) => $s->prefix = 'min-w');
    }

    /**
     * Set this unit for max-width context.
     */
    public function asMaxWidth(): static
    {
        return $this->with(fn($s) => $s->prefix = 'max-w');
    }

    /**
     * Set this unit for min-height context.
     */
    public function asMinHeight(): static
    {
        return $this->with(fn($s) => $s->prefix = 'min-h');
    }

    /**
     * Set this unit for max-height context.
     */
    public function asMaxHeight(): static
    {
        return $this->with(fn($s) => $s->prefix = 'max-h');
    }

    /**
     * Set this unit for padding context.
     */
    public function asPadding(): static
    {
        return $this->with(fn($s) => $s->prefix = 'p');
    }

    /**
     * Set this unit for padding-x context.
     */
    public function asPaddingX(): static
    {
        return $this->with(fn($s) => $s->prefix = 'px');
    }

    /**
     * Set this unit for padding-y context.
     */
    public function asPaddingY(): static
    {
        return $this->with(fn($s) => $s->prefix = 'py');
    }

    /**
     * Set this unit for margin context.
     */
    public function asMargin(): static
    {
        return $this->with(fn($s) => $s->prefix = 'm');
    }

    /**
     * Set this unit for margin-x context.
     */
    public function asMarginX(): static
    {
        return $this->with(fn($s) => $s->prefix = 'mx');
    }

    /**
     * Set this unit for margin-y context.
     */
    public function asMarginY(): static
    {
        return $this->with(fn($s) => $s->prefix = 'my');
    }

    /**
     * Set this unit for gap context.
     */
    public function asGap(): static
    {
        return $this->with(fn($s) => $s->prefix = 'gap');
    }

    /**
     * Set this unit for horizontal gap context.
     */
    public function asGapX(): static
    {
        return $this->with(fn($s) => $s->prefix = 'gap-x');
    }

    /**
     * Set this unit for vertical gap context.
     */
    public function asGapY(): static
    {
        return $this->with(fn($s) => $s->prefix = 'gap-y');
    }

    /**
     * Set this unit for border-radius context.
     */
    public function asRadius(): static
    {
        return $this->with(fn($s) => $s->prefix = 'rounded');
    }

    protected function getBaseClass(): string
    {
        if ($this->prefix === '') {
            return $this->value;
        }
        return $this->prefix . '-' . $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    // No spacing
    public static function none(): static
    {
        return new static('0');
    }

    // Tailwind spacing scale (in rems, 4 = 1rem = 16px)
    public static function px(int $value): static
    {
        return new static((string)$value);
    }

    // Named sizes for common scales (use size prefix to avoid conflict with breakpoint methods)
    public static function sizeXs(): static
    {
        return new static('1'); // 0.25rem
    }

    public static function sizeSm(): static
    {
        return new static('2'); // 0.5rem
    }

    public static function sizeMd(): static
    {
        return new static('4'); // 1rem
    }

    public static function sizeLg(): static
    {
        return new static('6'); // 1.5rem
    }

    public static function sizeXl(): static
    {
        return new static('8'); // 2rem
    }

    public static function sizeXxl(): static
    {
        return new static('12'); // 3rem
    }

    // Numeric Tailwind scale shortcuts
    public static function size(int $value): static
    {
        return new static((string)$value);
    }

    public static function scale1(): static
    {
        return new static('1');
    }

    public static function scale2(): static
    {
        return new static('2');
    }

    public static function scale3(): static
    {
        return new static('3');
    }

    public static function scale4(): static
    {
        return new static('4');
    }

    public static function scale5(): static
    {
        return new static('5');
    }

    public static function scale6(): static
    {
        return new static('6');
    }

    public static function scale8(): static
    {
        return new static('8');
    }

    public static function scale10(): static
    {
        return new static('10');
    }

    public static function scale12(): static
    {
        return new static('12');
    }

    public static function scale16(): static
    {
        return new static('16');
    }

    public static function scale20(): static
    {
        return new static('20');
    }

    public static function scale24(): static
    {
        return new static('24');
    }

    // Special values
    public static function auto(): static
    {
        return new static('auto');
    }

    public static function full(): static
    {
        return new static('full');
    }

    public static function screen(): static
    {
        return new static('screen');
    }

    public static function min(): static
    {
        return new static('min');
    }

    public static function max(): static
    {
        return new static('max');
    }

    public static function fit(): static
    {
        return new static('fit');
    }

    // Fractional widths
    public static function half(): static
    {
        return new static('1/2');
    }

    public static function third(): static
    {
        return new static('1/3');
    }

    public static function twoThirds(): static
    {
        return new static('2/3');
    }

    public static function quarter(): static
    {
        return new static('1/4');
    }

    public static function threeQuarters(): static
    {
        return new static('3/4');
    }

    // Border radius named sizes
    public static function rounded(): static
    {
        return new static('md');
    }

    public static function roundedSm(): static
    {
        return new static('sm');
    }

    public static function roundedLg(): static
    {
        return new static('lg');
    }

    public static function roundedXl(): static
    {
        return new static('xl');
    }

    public static function roundedFull(): static
    {
        return new static('full');
    }

    public static function roundedNone(): static
    {
        return new static('none');
    }
}
