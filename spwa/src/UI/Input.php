<?php

namespace Spwa\UI;

/**
 * Input element for form fields.
 *
 * Usage:
 *   UI::input()
 *       ->type('email')
 *       ->name('email')
 *       ->placeholder('Enter your email')
 *       ->required()
 */
class Input extends UIElement
{
    protected string $inputType = 'text';
    protected ?string $inputName = null;
    protected ?string $inputValue = null;
    protected ?string $inputPlaceholder = null;
    protected ?string $inputId = null;
    protected bool $isRequired = false;
    protected bool $isDisabled = false;
    protected bool $isReadonly = false;
    protected bool $isAutofocus = false;
    protected ?string $inputAutocomplete = null;
    protected ?int $inputMinLength = null;
    protected ?int $inputMaxLength = null;
    protected ?string $inputPattern = null;
    protected ?string $inputMin = null;
    protected ?string $inputMax = null;
    protected ?string $inputStep = null;
    protected bool $isChecked = false;

    public function __construct()
    {
        parent::__construct('input');
    }

    /**
     * Set input type.
     */
    public function type(string $type): static
    {
        $this->inputType = $type;
        return $this;
    }

    /**
     * Set name attribute.
     */
    public function name(string $name): static
    {
        $this->inputName = $name;
        return $this;
    }

    /**
     * Set value attribute.
     */
    public function value(string $value): static
    {
        $this->inputValue = $value;
        return $this;
    }

    /**
     * Set placeholder text.
     */
    public function placeholder(string $placeholder): static
    {
        $this->inputPlaceholder = $placeholder;
        return $this;
    }

    /**
     * Set id attribute.
     */
    public function id(string $id): static
    {
        $this->inputId = $id;
        return $this;
    }

    /**
     * Mark as required.
     */
    public function required(bool $required = true): static
    {
        $this->isRequired = $required;
        return $this;
    }

    /**
     * Mark as disabled.
     */
    public function disabled(bool $disabled = true): static
    {
        $this->isDisabled = $disabled;
        return $this;
    }

    /**
     * Mark as readonly.
     */
    public function readonly(bool $readonly = true): static
    {
        $this->isReadonly = $readonly;
        return $this;
    }

    /**
     * Set autofocus.
     */
    public function autofocus(bool $autofocus = true): static
    {
        $this->isAutofocus = $autofocus;
        return $this;
    }

    /**
     * Set autocomplete behavior.
     */
    public function autocomplete(string $value): static
    {
        $this->inputAutocomplete = $value;
        return $this;
    }

    /**
     * Set minimum length.
     */
    public function minLength(int $length): static
    {
        $this->inputMinLength = $length;
        return $this;
    }

    /**
     * Set maximum length.
     */
    public function maxLength(int $length): static
    {
        $this->inputMaxLength = $length;
        return $this;
    }

    /**
     * Set pattern for validation.
     */
    public function pattern(string $pattern): static
    {
        $this->inputPattern = $pattern;
        return $this;
    }

    /**
     * Set min value (for number/date inputs).
     */
    public function min(string $value): static
    {
        $this->inputMin = $value;
        return $this;
    }

    /**
     * Set max value (for number/date inputs).
     */
    public function max(string $value): static
    {
        $this->inputMax = $value;
        return $this;
    }

    /**
     * Set step value (for number inputs).
     */
    public function step(string $value): static
    {
        $this->inputStep = $value;
        return $this;
    }

    /**
     * Set checked state (for checkbox/radio).
     */
    public function checked(bool $checked = true): static
    {
        $this->isChecked = $checked;
        return $this;
    }

    // ============================================================
    // Type shortcuts
    // ============================================================

    public function text(): static
    {
        return $this->type('text');
    }

    public function email(): static
    {
        return $this->type('email');
    }

    public function password(): static
    {
        return $this->type('password');
    }

    public function number(): static
    {
        return $this->type('number');
    }

    public function tel(): static
    {
        return $this->type('tel');
    }

    public function url(): static
    {
        return $this->type('url');
    }

    public function search(): static
    {
        return $this->type('search');
    }

    public function date(): static
    {
        return $this->type('date');
    }

    public function time(): static
    {
        return $this->type('time');
    }

    public function datetime(): static
    {
        return $this->type('datetime-local');
    }

    public function checkbox(): static
    {
        return $this->type('checkbox');
    }

    public function radio(): static
    {
        return $this->type('radio');
    }

    public function file(): static
    {
        return $this->type('file');
    }

    public function hiddenInput(): static
    {
        return $this->type('hidden');
    }

    public function colorInput(): static
    {
        return $this->type('color');
    }

    public function range(): static
    {
        return $this->type('range');
    }

    /**
     * Render to HTML string.
     */
    public function toHtml(): string
    {
        $this->attr('type', $this->inputType);

        if ($this->inputName !== null) {
            $this->attr('name', $this->inputName);
        }

        if ($this->inputValue !== null) {
            $this->attr('value', $this->inputValue);
        }

        if ($this->inputPlaceholder !== null) {
            $this->attr('placeholder', $this->inputPlaceholder);
        }

        if ($this->inputId !== null) {
            $this->attr('id', $this->inputId);
        }

        if ($this->isRequired) {
            $this->attr('required', 'required');
        }

        if ($this->isDisabled) {
            $this->attr('disabled', 'disabled');
        }

        if ($this->isReadonly) {
            $this->attr('readonly', 'readonly');
        }

        if ($this->isAutofocus) {
            $this->attr('autofocus', 'autofocus');
        }

        if ($this->inputAutocomplete !== null) {
            $this->attr('autocomplete', $this->inputAutocomplete);
        }

        if ($this->inputMinLength !== null) {
            $this->attr('minlength', (string)$this->inputMinLength);
        }

        if ($this->inputMaxLength !== null) {
            $this->attr('maxlength', (string)$this->inputMaxLength);
        }

        if ($this->inputPattern !== null) {
            $this->attr('pattern', $this->inputPattern);
        }

        if ($this->inputMin !== null) {
            $this->attr('min', $this->inputMin);
        }

        if ($this->inputMax !== null) {
            $this->attr('max', $this->inputMax);
        }

        if ($this->inputStep !== null) {
            $this->attr('step', $this->inputStep);
        }

        if ($this->isChecked && in_array($this->inputType, ['checkbox', 'radio'])) {
            $this->attr('checked', 'checked');
        }

        return parent::toHtml();
    }
}
