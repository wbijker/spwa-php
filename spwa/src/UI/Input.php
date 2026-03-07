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
    protected string $type = 'text';
    protected ?string $name = null;
    protected ?string $value = null;
    protected ?string $placeholder = null;
    protected ?string $id = null;
    protected bool $required = false;
    protected bool $disabled = false;
    protected bool $readonly = false;
    protected bool $autofocus = false;
    protected ?string $autocomplete = null;
    protected ?int $minLength = null;
    protected ?int $maxLength = null;
    protected ?string $pattern = null;
    protected ?string $min = null;
    protected ?string $max = null;
    protected ?string $step = null;
    protected bool $checked = false;

    /**
     * Set input type.
     */
    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set name attribute.
     */
    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set value attribute.
     */
    public function value(string $value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Set placeholder text.
     */
    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * Set id attribute.
     */
    public function id(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Mark as required.
     */
    public function required(bool $required = true): static
    {
        $this->required = $required;
        return $this;
    }

    /**
     * Mark as disabled.
     */
    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * Mark as readonly.
     */
    public function readonly(bool $readonly = true): static
    {
        $this->readonly = $readonly;
        return $this;
    }

    /**
     * Set autofocus.
     */
    public function autofocus(bool $autofocus = true): static
    {
        $this->autofocus = $autofocus;
        return $this;
    }

    /**
     * Set autocomplete behavior.
     */
    public function autocomplete(string $value): static
    {
        $this->autocomplete = $value;
        return $this;
    }

    /**
     * Set minimum length.
     */
    public function minLength(int $length): static
    {
        $this->minLength = $length;
        return $this;
    }

    /**
     * Set maximum length.
     */
    public function maxLength(int $length): static
    {
        $this->maxLength = $length;
        return $this;
    }

    /**
     * Set pattern for validation.
     */
    public function pattern(string $pattern): static
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * Set min value (for number/date inputs).
     */
    public function min(string $value): static
    {
        $this->min = $value;
        return $this;
    }

    /**
     * Set max value (for number/date inputs).
     */
    public function max(string $value): static
    {
        $this->max = $value;
        return $this;
    }

    /**
     * Set step value (for number inputs).
     */
    public function step(string $value): static
    {
        $this->step = $value;
        return $this;
    }

    /**
     * Set checked state (for checkbox/radio).
     */
    public function checked(bool $checked = true): static
    {
        $this->checked = $checked;
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

    public function hidden(): static
    {
        return $this->type('hidden');
    }

    public function color(): static
    {
        return $this->type('color');
    }

    public function range(): static
    {
        return $this->type('range');
    }

    public function render(): Node
    {
        $node = $this->node('input')->attr('type', $this->type);

        if ($this->name !== null) {
            $node->attr('name', $this->name);
        }

        if ($this->value !== null) {
            $node->attr('value', $this->value);
        }

        if ($this->placeholder !== null) {
            $node->attr('placeholder', $this->placeholder);
        }

        if ($this->id !== null) {
            $node->attr('id', $this->id);
        }

        if ($this->required) {
            $node->attr('required', 'required');
        }

        if ($this->disabled) {
            $node->attr('disabled', 'disabled');
        }

        if ($this->readonly) {
            $node->attr('readonly', 'readonly');
        }

        if ($this->autofocus) {
            $node->attr('autofocus', 'autofocus');
        }

        if ($this->autocomplete !== null) {
            $node->attr('autocomplete', $this->autocomplete);
        }

        if ($this->minLength !== null) {
            $node->attr('minlength', (string)$this->minLength);
        }

        if ($this->maxLength !== null) {
            $node->attr('maxlength', (string)$this->maxLength);
        }

        if ($this->pattern !== null) {
            $node->attr('pattern', $this->pattern);
        }

        if ($this->min !== null) {
            $node->attr('min', $this->min);
        }

        if ($this->max !== null) {
            $node->attr('max', $this->max);
        }

        if ($this->step !== null) {
            $node->attr('step', $this->step);
        }

        if ($this->checked && in_array($this->type, ['checkbox', 'radio'])) {
            $node->attr('checked', 'checked');
        }

        return $node;
    }
}
