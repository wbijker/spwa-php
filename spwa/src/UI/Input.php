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
    use FormControl;

    protected string $inputType = 'text';
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

    protected function applyAttributes(): void
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
    }
}
