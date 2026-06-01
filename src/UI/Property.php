<?php

namespace BrickPHP\UI;

/**
 * Base class for value types that resolve to a CSS class name (colors,
 * units, alignment values, etc.). Modifier prefixes (hover, dark, sm,
 * has(), …) live on the Pseudo argument passed alongside the value at
 * the call site — they no longer ride on the value itself.
 */
abstract class Property
{
    /**
     * Clone-and-modify helper. Used by value subclasses (Color::opacity,
     * Color::alpha, …) that produce a derived instance without mutating
     * the original cached singleton.
     */
    protected function derive(callable $modifier): static
    {
        $clone = clone $this;
        $modifier($clone);
        return $clone;
    }

    /**
     * The class-name fragment for this value (e.g. "red-500", "16px",
     * "flex-row"). Subclasses produce the part after the context prefix.
     */
    abstract protected function base(): string;

    /**
     * The full class name with no modifier prefix. Subclasses with a
     * context (Color/Unit/Align) ignore this in favour of withContext().
     */
    public function toClass(): string
    {
        return $this->base();
    }

    public function __toString(): string
    {
        return $this->toClass();
    }

    /**
     * Escape class name for CSS selector.
     */
    public static function escapeClassName(string $class): string
    {
        return preg_replace('/([.:\[\]\/()>,+~])/', '\\\\$1', $class);
    }
}
