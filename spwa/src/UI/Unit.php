<?php

namespace Spwa\UI;

/**
 * Unit represents a measurement value with a numeric amount and unit of measure.
 *
 * Usage:
 *   Unit::px(16)           // 16 pixels
 *   Unit::rem(2)           // 2rem
 *   Unit::scale(4)         // Tailwind scale value (4 = 1rem)
 *   Unit::percent(50)      // 50%
 *   Unit::full()           // 100%
 */
class Unit extends Property
{
    public function __construct(
        protected string $value
    ) {
    }

    protected function base(): string
    {
        return $this->value;
    }

    /**
     * Get the raw value.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Get the actual CSS value.
     */
    public function getCssValue(): string
    {
        // Handle arbitrary values like [16px]
        if (str_starts_with($this->value, '[') && str_ends_with($this->value, ']')) {
            return substr($this->value, 1, -1);
        }

        // Spacing scale
        $scale = [
            '0' => '0px',
            'px' => '1px',
            '0.5' => '0.125rem',
            '1' => '0.25rem',
            '1.5' => '0.375rem',
            '2' => '0.5rem',
            '2.5' => '0.625rem',
            '3' => '0.75rem',
            '3.5' => '0.875rem',
            '4' => '1rem',
            '5' => '1.25rem',
            '6' => '1.5rem',
            '7' => '1.75rem',
            '8' => '2rem',
            '9' => '2.25rem',
            '10' => '2.5rem',
            '11' => '2.75rem',
            '12' => '3rem',
            '14' => '3.5rem',
            '16' => '4rem',
            '20' => '5rem',
            '24' => '6rem',
            '28' => '7rem',
            '32' => '8rem',
            '36' => '9rem',
            '40' => '10rem',
            '44' => '11rem',
            '48' => '12rem',
            '52' => '13rem',
            '56' => '14rem',
            '60' => '15rem',
            '64' => '16rem',
            '72' => '18rem',
            '80' => '20rem',
            '96' => '24rem',
        ];

        // Special size values
        $sizes = [
            'full' => '100%',
            'screen' => '100vw',
            'min' => 'min-content',
            'max' => 'max-content',
            'fit' => 'fit-content',
            'auto' => 'auto',
            '1/2' => '50%',
            '1/3' => '33.333333%',
            '2/3' => '66.666667%',
            '1/4' => '25%',
            '2/4' => '50%',
            '3/4' => '75%',
            '1/5' => '20%',
            '2/5' => '40%',
            '3/5' => '60%',
            '4/5' => '80%',
            '1/6' => '16.666667%',
            '5/6' => '83.333333%',
        ];

        // Border radius values
        $radii = [
            'none' => '0px',
            'sm' => '0.125rem',
            'md' => '0.375rem',
            'lg' => '0.5rem',
            'xl' => '0.75rem',
            '2xl' => '1rem',
            '3xl' => '1.5rem',
        ];

        return $scale[$this->value] ?? $sizes[$this->value] ?? $radii[$this->value] ?? $this->value;
    }

    /**
     * Build class with specific context prefix.
     */
    public function withContext(string $context): string
    {
        return $this->prefix() . $context . '-' . $this->value;
    }

    // ============================================================
    // Tailwind scale (value * 0.25rem)
    // ============================================================

    /**
     * Tailwind spacing scale (1 = 0.25rem, 4 = 1rem, etc.).
     */
    public static function scale(int $value): static
    {
        return new static((string)$value);
    }

    /**
     * No size/spacing (0).
     */
    public static function none(): static
    {
        return new static('0');
    }

    public static function value(int $value): static
    {
        return new static($value);
    }

    /**
     * Scale 1 (0.25rem).
     */
    public static function xs(): static
    {
        return new static('1');
    }

    /**
     * Scale 2 (0.5rem).
     */
    public static function small(): static
    {
        return new static('2');
    }

    /**
     * Scale 4 (1rem).
     */
    public static function medium(): static
    {
        return new static('4');
    }

    /**
     * Scale 6 (1.5rem).
     */
    public static function large(): static
    {
        return new static('6');
    }

    /**
     * Scale 8 (2rem).
     */
    public static function extraLarge(): static
    {
        return new static('8');
    }

    /**
     * Scale 12 (3rem).
     */
//    public static function xxl(): static
//    {
//        return new static('12');
//    }

    /**
     * Scale 16 (4rem).
     */
    public static function xxxl(): static
    {
        return new static('16');
    }

    // ============================================================
    // Absolute units
    // ============================================================

    /**
     * Pixels.
     */
    public static function px(int $value): static
    {
        return new static('[' . $value . 'px]');
    }

    /**
     * Rem units.
     */
    public static function rem(float $value): static
    {
        return new static('[' . $value . 'rem]');
    }

    // ============================================================
    // Relative/percentage units
    // ============================================================

    /**
     * Percentage.
     */
    public static function percent(int $value): static
    {
        return new static('[' . $value . '%]');
    }

    /**
     * Full (100%).
     */
    public static function full(): static
    {
        return new static('full');
    }

    /**
     * Half (50%).
     */
    public static function half(): static
    {
        return new static('1/2');
    }

    /**
     * Third (33.33%).
     */
    public static function third(): static
    {
        return new static('1/3');
    }

    /**
     * Two thirds (66.66%).
     */
    public static function twoThirds(): static
    {
        return new static('2/3');
    }

    /**
     * Quarter (25%).
     */
    public static function quarter(): static
    {
        return new static('1/4');
    }

    /**
     * Three quarters (75%).
     */
    public static function threeQuarters(): static
    {
        return new static('3/4');
    }

    // ============================================================
    // Special values
    // ============================================================

    /**
     * Auto sizing.
     */
    public static function auto(): static
    {
        return new static('auto');
    }

    /**
     * Viewport width/height.
     */
    public static function screen(): static
    {
        return new static('screen');
    }

    /**
     * Min content.
     */
    public static function min(): static
    {
        return new static('min');
    }

    /**
     * Max content.
     */
    public static function max(): static
    {
        return new static('max');
    }

    /**
     * Fit content.
     */
    public static function fit(): static
    {
        return new static('fit');
    }

    // ============================================================
    // Border radius presets
    // ============================================================

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
