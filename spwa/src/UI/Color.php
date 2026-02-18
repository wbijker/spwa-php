<?php

namespace Spwa\UI;

/**
 * Stateful color value with Tailwind color palette support.
 *
 * Usage:
 *   Color::red(500)           // bg-red-500
 *   Color::red(500)->hover()  // hover:bg-red-500
 *   Color::blue(300)->dark()  // dark:bg-blue-300
 */
class Color extends StateValue
{
    protected string $prefix;

    public function __construct(
        protected string $name,
        protected ?int $shade = null,
        protected ?int $opacity = null
    ) {
        $this->prefix = 'bg';
    }

    /**
     * Set color to be used for text instead of background.
     */
    public function asText(): static
    {
        return $this->with(fn($s) => $s->prefix = 'text');
    }

    /**
     * Set color to be used for border.
     */
    public function asBorder(): static
    {
        return $this->with(fn($s) => $s->prefix = 'border');
    }

    /**
     * Set color to be used for outline.
     */
    public function asOutline(): static
    {
        return $this->with(fn($s) => $s->prefix = 'outline');
    }

    /**
     * Set color to be used for ring.
     */
    public function asRing(): static
    {
        return $this->with(fn($s) => $s->prefix = 'ring');
    }

    /**
     * Set color to be used for decoration (underline).
     */
    public function asDecoration(): static
    {
        return $this->with(fn($s) => $s->prefix = 'decoration');
    }

    /**
     * Set opacity.
     */
    public function opacity(int $value): static
    {
        return $this->with(fn($s) => $s->opacity = $value);
    }

    protected function getBaseClass(): string
    {
        $class = $this->prefix . '-' . $this->name;

        if ($this->shade !== null) {
            $class .= '-' . $this->shade;
        }

        if ($this->opacity !== null) {
            $class .= '/' . $this->opacity;
        }

        return $class;
    }

    // Basic colors
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

    // Tailwind color palette
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

    /**
     * Create a gradient background.
     * Note: This is a placeholder - real gradients would need custom CSS.
     */
    public static function gradient(string $from = 'blue', string $to = 'purple'): static
    {
        $color = new static('gradient-to-r');
        $color->prefix = 'bg';
        return $color;
    }
}
