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
}
