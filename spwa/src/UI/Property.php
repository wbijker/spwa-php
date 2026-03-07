<?php

namespace Spwa\UI;

/**
 * Base class for all property values (colors, units, etc.).
 * Provides fluent chainable methods for CSS pseudo-classes, breakpoints, and selectors.
 *
 * Usage:
 *   Color::red(500)->hover()->dark()
 *   Unit::size(4)->md()->hover()
 */
abstract class Property
{
    protected ?Breakpoint $breakpoint = null;
    protected ?ColorScheme $colorScheme = null;
    /** @var Pseudo[] */
    protected array $pseudos = [];

    /**
     * Create a modified clone.
     */
    protected function derive(callable $modifier): static
    {
        $clone = clone $this;
        $modifier($clone);
        return $clone;
    }

    // ============================================================
    // Breakpoints (responsive design)
    // ============================================================

    public function sm(): static
    {
        return $this->derive(fn($p) => $p->breakpoint = Breakpoint::Small);
    }

    public function md(): static
    {
        return $this->derive(fn($p) => $p->breakpoint = Breakpoint::Medium);
    }

    public function lg(): static
    {
        return $this->derive(fn($p) => $p->breakpoint = Breakpoint::Large);
    }

    public function xl(): static
    {
        return $this->derive(fn($p) => $p->breakpoint = Breakpoint::ExtraLarge);
    }

    public function xxl(): static
    {
        return $this->derive(fn($p) => $p->breakpoint = Breakpoint::TwoXL);
    }

    // ============================================================
    // Color Scheme
    // ============================================================

    public function dark(): static
    {
        return $this->derive(fn($p) => $p->colorScheme = ColorScheme::Dark);
    }

    public function light(): static
    {
        return $this->derive(fn($p) => $p->colorScheme = ColorScheme::Light);
    }

    // ============================================================
    // Pseudo Classes - Interaction
    // ============================================================

    public function hover(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Hover);
    }

    public function active(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Active);
    }

    public function focus(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Focus);
    }

    public function focusVisible(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::FocusVisible);
    }

    public function focusWithin(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::FocusWithin);
    }

    public function visited(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Visited);
    }

    // ============================================================
    // Pseudo Classes - State
    // ============================================================

    public function disabled(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Disabled);
    }

    public function enabled(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Enabled);
    }

    public function checked(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Checked);
    }

    public function required(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Required);
    }

    public function valid(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Valid);
    }

    public function invalid(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Invalid);
    }

    public function placeholder(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Placeholder);
    }

    // ============================================================
    // Pseudo Classes - Structural
    // ============================================================

    public function first(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::FirstChild);
    }

    public function last(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::LastChild);
    }

    public function only(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::OnlyChild);
    }

    public function odd(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Odd);
    }

    public function even(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Even);
    }

    public function empty(): static
    {
        return $this->derive(fn($p) => $p->pseudos[] = Pseudo::Empty);
    }

    // ============================================================
    // Class Generation
    // ============================================================

    /**
     * Build the selector prefix (breakpoint:colorScheme:pseudo:).
     */
    protected function prefix(): string
    {
        $parts = [];

        if ($this->breakpoint !== null) {
            $parts[] = $this->breakpoint->value;
        }

        if ($this->colorScheme !== null) {
            $parts[] = $this->colorScheme->value;
        }

        foreach ($this->pseudos as $pseudo) {
            $parts[] = $pseudo->value;
        }

        return $parts ? implode(':', $parts) . ':' : '';
    }

    /**
     * Get the base class value (without prefix).
     */
    abstract protected function base(): string;

    /**
     * Get the full class including prefix.
     */
    public function toClass(): string
    {
        return $this->prefix() . $this->base();
    }

    public function __toString(): string
    {
        return $this->toClass();
    }

    // ============================================================
    // CSS Selector Generation
    // ============================================================

    /**
     * Get the CSS selector for this property.
     * Handles pseudo-classes and pseudo-elements.
     */
    public function getCssSelector(string $className): string
    {
        $selector = '.' . self::escapeClassName($className);

        foreach ($this->pseudos as $pseudo) {
            $selector .= self::getPseudoSelector($pseudo);
        }

        return $selector;
    }

    /**
     * Get CSS pseudo selector string.
     */
    private static function getPseudoSelector(Pseudo $pseudo): string
    {
        return match ($pseudo) {
            Pseudo::Hover => ':hover',
            Pseudo::Active => ':active',
            Pseudo::Focus => ':focus',
            Pseudo::FocusVisible => ':focus-visible',
            Pseudo::FocusWithin => ':focus-within',
            Pseudo::Visited => ':visited',
            Pseudo::Disabled => ':disabled',
            Pseudo::Enabled => ':enabled',
            Pseudo::Checked => ':checked',
            Pseudo::Required => ':required',
            Pseudo::Valid => ':valid',
            Pseudo::Invalid => ':invalid',
            Pseudo::Placeholder => '::placeholder',
            Pseudo::FirstChild => ':first-child',
            Pseudo::LastChild => ':last-child',
            Pseudo::OnlyChild => ':only-child',
            Pseudo::Odd => ':nth-child(odd)',
            Pseudo::Even => ':nth-child(even)',
            Pseudo::Empty => ':empty',
        };
    }

    /**
     * Get the media query wrapper if breakpoint is set.
     */
    public function getMediaQuery(): ?string
    {
        if ($this->breakpoint === null && $this->colorScheme === null) {
            return null;
        }

        $conditions = [];

        if ($this->breakpoint !== null) {
            $conditions[] = match ($this->breakpoint) {
                Breakpoint::Small => '(min-width: 640px)',
                Breakpoint::Medium => '(min-width: 768px)',
                Breakpoint::Large => '(min-width: 1024px)',
                Breakpoint::ExtraLarge => '(min-width: 1280px)',
                Breakpoint::TwoXL => '(min-width: 1536px)',
            };
        }

        if ($this->colorScheme !== null) {
            $conditions[] = match ($this->colorScheme) {
                ColorScheme::Dark => '(prefers-color-scheme: dark)',
                ColorScheme::Light => '(prefers-color-scheme: light)',
            };
        }

        return '@media ' . implode(' and ', $conditions);
    }

    /**
     * Check if this property has modifiers (breakpoint, color scheme, or pseudos).
     */
    public function hasModifiers(): bool
    {
        return $this->breakpoint !== null
            || $this->colorScheme !== null
            || !empty($this->pseudos);
    }

    /**
     * Get breakpoint if set.
     */
    public function getBreakpoint(): ?Breakpoint
    {
        return $this->breakpoint;
    }

    /**
     * Get color scheme if set.
     */
    public function getColorScheme(): ?ColorScheme
    {
        return $this->colorScheme;
    }

    /**
     * Get pseudo classes.
     * @return Pseudo[]
     */
    public function getPseudos(): array
    {
        return $this->pseudos;
    }

    /**
     * Escape class name for CSS selector.
     */
    public static function escapeClassName(string $class): string
    {
        return preg_replace('/([.:\[\]\/])/', '\\\\$1', $class);
    }
}
