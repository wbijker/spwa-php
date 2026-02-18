<?php

namespace Spwa\UI;

/**
 * Abstract base class for all UI elements.
 */
abstract class BaseElement
{
    /** @var string[] Collected CSS classes */
    protected array $classes = [];

    /**
     * Add a class to this element.
     */
    public function addClass(string $class): static
    {
        if (!in_array($class, $this->classes, true)) {
            $this->classes[] = $class;
        }
        return $this;
    }

    /**
     * Add classes from a StateValue.
     */
    protected function addStateValue(StateValue $value): void
    {
        $this->addClass($value->toClass());
    }

    /**
     * Get all collected classes.
     * @return string[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * Build class attribute string.
     */
    protected function buildClassAttribute(): string
    {
        return implode(' ', $this->classes);
    }

    /**
     * Render the element to HTML.
     */
    abstract public function render(): void;
}
