<?php

namespace Spwa\UI;

/**
 * Color property representing a color value only.
 * Context (background, text, border) is determined by the UIElement method.
 *
 * Usage:
 *   Color::red(500)              // red-500
 *   Color::red(500)->hover()     // hover:red-500
 *   Color::blue(300)->dark()     // dark:blue-300
 */
class Color extends Property
{
    public function __construct(
        protected string $name,
        protected ?int $shade = null,
        protected ?int $opacity = null
    ) {
    }

    protected function base(): string
    {
        $class = $this->name;

        if ($this->shade !== null) {
            $class .= '-' . $this->shade;
        }

        if ($this->opacity !== null) {
            $class .= '/' . $this->opacity;
        }

        return $class;
    }

    /**
     * Get the color name (for use in UIElement methods).
     */
    public function getName(): string
    {
        return $this->base();
    }

    /**
     * Build class with specific context prefix.
     */
    public function withContext(string $context): string
    {
        return $this->prefix() . $context . '-' . $this->base();
    }

    /**
     * Set opacity (0-100).
     */
    public function opacity(int $value): static
    {
        return $this->derive(fn($c) => $c->opacity = $value);
    }

    // ============================================================
    // Basic colors
    // ============================================================

    public static function transparent(): static
    {
        return new static('transparent');
    }

    public static function current(): static
    {
        return new static('current');
    }

    public static function inherit(): static
    {
        return new static('inherit');
    }

    public static function white(): static
    {
        return new static('white');
    }

    public static function black(): static
    {
        return new static('black');
    }

    // ============================================================
    // Color palette (50-950 shades)
    // ============================================================

    public static function slate(int $shade = 500): static
    {
        return new static('slate', $shade);
    }

    public static function gray(int $shade = 500): static
    {
        return new static('gray', $shade);
    }

    public static function zinc(int $shade = 500): static
    {
        return new static('zinc', $shade);
    }

    public static function neutral(int $shade = 500): static
    {
        return new static('neutral', $shade);
    }

    public static function stone(int $shade = 500): static
    {
        return new static('stone', $shade);
    }

    public static function red(int $shade = 500): static
    {
        return new static('red', $shade);
    }

    public static function orange(int $shade = 500): static
    {
        return new static('orange', $shade);
    }

    public static function amber(int $shade = 500): static
    {
        return new static('amber', $shade);
    }

    public static function yellow(int $shade = 500): static
    {
        return new static('yellow', $shade);
    }

    public static function lime(int $shade = 500): static
    {
        return new static('lime', $shade);
    }

    public static function green(int $shade = 500): static
    {
        return new static('green', $shade);
    }

    public static function emerald(int $shade = 500): static
    {
        return new static('emerald', $shade);
    }

    public static function teal(int $shade = 500): static
    {
        return new static('teal', $shade);
    }

    public static function cyan(int $shade = 500): static
    {
        return new static('cyan', $shade);
    }

    public static function sky(int $shade = 500): static
    {
        return new static('sky', $shade);
    }

    public static function blue(int $shade = 500): static
    {
        return new static('blue', $shade);
    }

    public static function indigo(int $shade = 500): static
    {
        return new static('indigo', $shade);
    }

    public static function violet(int $shade = 500): static
    {
        return new static('violet', $shade);
    }

    public static function purple(int $shade = 500): static
    {
        return new static('purple', $shade);
    }

    public static function fuchsia(int $shade = 500): static
    {
        return new static('fuchsia', $shade);
    }

    public static function pink(int $shade = 500): static
    {
        return new static('pink', $shade);
    }

    public static function rose(int $shade = 500): static
    {
        return new static('rose', $shade);
    }
}
