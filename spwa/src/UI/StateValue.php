<?php

namespace Spwa\UI;

/**
 * Base class for all stateful CSS property values.
 * Provides chainable methods for pseudo-classes, breakpoints, and color schemes.
 *
 * Usage: Color::red(500)->hover()->dark()->sm()
 */
abstract class StateValue
{
    /** @var PseudoClass[] */
    protected array $pseudoClasses = [];
    protected ?Breakpoint $breakpoint = null;
    protected ?ColorScheme $colorScheme = null;
    protected ?PseudoElement $pseudoElement = null;

    /**
     * Create a clone with modifications applied.
     */
    protected function with(callable $modifier): static
    {
        $clone = clone $this;
        $modifier($clone);
        return $clone;
    }

    // Pseudo-classes
    public function hover(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::Hover);
    }

    public function active(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::Active);
    }

    public function focus(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::Focus);
    }

    public function focusVisible(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::FocusVisible);
    }

    public function focusWithin(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::FocusWithin);
    }

    public function visited(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::Visited);
    }

    public function disabled(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::Disabled);
    }

    public function enabled(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::Enabled);
    }

    public function checked(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::Checked);
    }

    public function required(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::Required);
    }

    public function valid(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::Valid);
    }

    public function invalid(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::Invalid);
    }

    public function firstChild(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::FirstChild);
    }

    public function lastChild(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::LastChild);
    }

    public function onlyChild(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::OnlyChild);
    }

    public function odd(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::OddChild);
    }

    public function even(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::EvenChild);
    }

    public function empty(): static
    {
        return $this->with(fn($s) => $s->pseudoClasses[] = PseudoClass::Empty);
    }

    // Breakpoints
    public function sm(): static
    {
        return $this->with(fn($s) => $s->breakpoint = Breakpoint::Small);
    }

    public function md(): static
    {
        return $this->with(fn($s) => $s->breakpoint = Breakpoint::Medium);
    }

    public function lg(): static
    {
        return $this->with(fn($s) => $s->breakpoint = Breakpoint::Large);
    }

    public function xl(): static
    {
        return $this->with(fn($s) => $s->breakpoint = Breakpoint::ExtraLarge);
    }

    public function xxl(): static
    {
        return $this->with(fn($s) => $s->breakpoint = Breakpoint::TwoXL);
    }

    // Color schemes
    public function dark(): static
    {
        return $this->with(fn($s) => $s->colorScheme = ColorScheme::Dark);
    }

    public function light(): static
    {
        return $this->with(fn($s) => $s->colorScheme = ColorScheme::Light);
    }

    // Pseudo-elements
    public function before(): static
    {
        return $this->with(fn($s) => $s->pseudoElement = PseudoElement::Before);
    }

    public function after(): static
    {
        return $this->with(fn($s) => $s->pseudoElement = PseudoElement::After);
    }

    public function placeholder(): static
    {
        return $this->with(fn($s) => $s->pseudoElement = PseudoElement::Placeholder);
    }

    public function selection(): static
    {
        return $this->with(fn($s) => $s->pseudoElement = PseudoElement::Selection);
    }

    /**
     * Build the Tailwind prefix for this state.
     * Returns something like "hover:dark:sm:" or "" if no state modifiers.
     */
    protected function buildStatePrefix(): string
    {
        $prefixes = [];

        // Breakpoint first (mobile-first approach)
        if ($this->breakpoint !== null) {
            $prefixes[] = $this->breakpoint->value;
        }

        // Color scheme
        if ($this->colorScheme !== null) {
            $prefixes[] = $this->colorScheme->value;
        }

        // Pseudo-classes
        foreach ($this->pseudoClasses as $pseudo) {
            $prefixes[] = $pseudo->value;
        }

        // Pseudo-element
        if ($this->pseudoElement !== null) {
            $prefixes[] = $this->pseudoElement->value;
        }

        return $prefixes ? implode(':', $prefixes) . ':' : '';
    }

    /**
     * Get the base CSS class value (without state prefixes).
     */
    abstract protected function getBaseClass(): string;

    /**
     * Get the full Tailwind class including state prefixes.
     */
    public function toClass(): string
    {
        return $this->buildStatePrefix() . $this->getBaseClass();
    }

    public function __toString(): string
    {
        return $this->toClass();
    }
}
