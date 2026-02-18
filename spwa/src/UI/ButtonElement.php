<?php

namespace Spwa\UI;

/**
 * Button element with common button styles.
 *
 * Usage:
 *   UI::button("Click me")
 *       ->background(Color::blue(500), Color::blue(600)->hover())
 *       ->color(Color::white())
 *       ->padding(Unit::md())
 *       ->rounded(Unit::rounded())
 */
class ButtonElement extends BaseStyledElement
{
    public function __construct(
        protected string $text,
        protected string $type = 'button'
    ) {
        $this->addClass('cursor-pointer');
    }

    /**
     * Set button type.
     */
    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
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

    /**
     * Disable the button.
     */
    public function disabled(): static
    {
        $this->addClass('disabled:opacity-50');
        $this->addClass('disabled:cursor-not-allowed');
        return $this;
    }

    /**
     * Apply primary button styles.
     */
    public function primary(): static
    {
        $this->addClass('bg-blue-500');
        $this->addClass('hover:bg-blue-600');
        $this->addClass('text-white');
        $this->addClass('px-4');
        $this->addClass('py-2');
        $this->addClass('rounded');
        return $this;
    }

    /**
     * Apply secondary button styles.
     */
    public function secondary(): static
    {
        $this->addClass('bg-gray-200');
        $this->addClass('hover:bg-gray-300');
        $this->addClass('text-gray-800');
        $this->addClass('px-4');
        $this->addClass('py-2');
        $this->addClass('rounded');
        return $this;
    }

    /**
     * Apply danger button styles.
     */
    public function danger(): static
    {
        $this->addClass('bg-red-500');
        $this->addClass('hover:bg-red-600');
        $this->addClass('text-white');
        $this->addClass('px-4');
        $this->addClass('py-2');
        $this->addClass('rounded');
        return $this;
    }

    /**
     * Apply outline button styles.
     */
    public function outline(): static
    {
        $this->addClass('bg-transparent');
        $this->addClass('border');
        $this->addClass('border-current');
        $this->addClass('hover:bg-gray-100');
        $this->addClass('px-4');
        $this->addClass('py-2');
        $this->addClass('rounded');
        return $this;
    }

    /**
     * Apply ghost button styles.
     */
    public function ghost(): static
    {
        $this->addClass('bg-transparent');
        $this->addClass('hover:bg-gray-100');
        $this->addClass('px-4');
        $this->addClass('py-2');
        $this->addClass('rounded');
        return $this;
    }

    /**
     * Set font weight.
     */
    public function weight(FontWeight $weight): static
    {
        $this->addStateValue($weight);
        return $this;
    }

    /**
     * Set font size.
     */
    public function size(FontSize $size): static
    {
        $this->addStateValue($size);
        return $this;
    }

    public function render(): void
    {
        $classAttr = $this->buildClassAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        echo "<button type=\"{$this->type}\"{$classHtml}>";
        echo htmlspecialchars($this->text, ENT_QUOTES, 'UTF-8');
        echo "</button>";
    }
}
