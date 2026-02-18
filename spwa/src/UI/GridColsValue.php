<?php

namespace Spwa\UI;

/**
 * Stateful grid columns value with responsive support.
 *
 * Usage:
 *   GridColsValue::cols(1)                    // grid-cols-1
 *   GridColsValue::cols(2)->md()              // md:grid-cols-2
 *   GridColsValue::cols(3)->lg()              // lg:grid-cols-3
 */
class GridColsValue extends StateValue
{
    public function __construct(
        protected int $count
    ) {
    }

    protected function getBaseClass(): string
    {
        return 'grid-cols-' . $this->count;
    }

    /**
     * Create a grid columns value.
     */
    public static function cols(int $count): static
    {
        return new static($count);
    }

    // Shorthand factory methods
    public static function one(): static
    {
        return new static(1);
    }

    public static function two(): static
    {
        return new static(2);
    }

    public static function three(): static
    {
        return new static(3);
    }

    public static function four(): static
    {
        return new static(4);
    }

    public static function five(): static
    {
        return new static(5);
    }

    public static function six(): static
    {
        return new static(6);
    }

    public static function twelve(): static
    {
        return new static(12);
    }
}
