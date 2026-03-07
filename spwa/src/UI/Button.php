<?php

namespace Spwa\UI;

/**
 * Button element.
 *
 * Usage:
 *   UI::button("Click Me")
 *       ->primary()
 *       ->padding(Unit::base())
 */
class Button extends UIElement
{
    protected ?string $type = 'button';

    public function __construct(
        protected string $label
    ) {
        $this->addStyle('cursor-pointer', ['cursor' => 'pointer']);
    }

    /**
     * Set as submit button.
     */
    public function submit(): static
    {
        $this->type = 'submit';
        return $this;
    }

    /**
     * Set as reset button.
     */
    public function reset(): static
    {
        $this->type = 'reset';
        return $this;
    }

    // ============================================================
    // Variants
    // ============================================================

    /**
     * Primary style.
     */
    public function primary(): static
    {
        $this->background(Color::blue(500), Color::blue(600)->hover());
        $this->color(Color::white());
        return $this;
    }

    /**
     * Secondary style.
     */
    public function secondary(): static
    {
        $this->background(Color::gray(200), Color::gray(300)->hover());
        $this->color(Color::gray(800));
        return $this;
    }

    /**
     * Danger/destructive style.
     */
    public function danger(): static
    {
        $this->background(Color::red(500), Color::red(600)->hover());
        $this->color(Color::white());
        return $this;
    }

    /**
     * Success style.
     */
    public function success(): static
    {
        $this->background(Color::green(500), Color::green(600)->hover());
        $this->color(Color::white());
        return $this;
    }

    /**
     * Outline style.
     */
    public function outline(): static
    {
        $this->background(Color::transparent(), Color::gray(100)->hover());
        $this->addStyle('border', ['border-width' => '1px', 'border-style' => 'solid']);
        $this->addStyle('border-current', ['border-color' => 'currentColor']);
        return $this;
    }

    /**
     * Ghost/text style.
     */
    public function ghost(): static
    {
        $this->background(Color::transparent(), Color::gray(100)->hover());
        return $this;
    }

    // ============================================================
    // States
    // ============================================================

    /**
     * Disabled appearance.
     */
    public function disabled(): static
    {
        $this->addStyle('opacity-50', ['opacity' => '0.5']);
        $this->addStyle('cursor-not-allowed', ['cursor' => 'not-allowed']);
        return $this;
    }

    /**
     * Loading state.
     */
    public function loading(): static
    {
        $this->addStyle('opacity-75', ['opacity' => '0.75']);
        $this->addStyle('cursor-wait', ['cursor' => 'wait']);
        return $this;
    }

    public function render(): string
    {
        $classAttr = $this->classAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';
        $typeAttr = $this->type ? " type=\"{$this->type}\"" : '';

        return "<button{$typeAttr}{$classHtml}>" . htmlspecialchars($this->label) . "</button>";
    }
}
