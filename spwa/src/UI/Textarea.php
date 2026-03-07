<?php

namespace Spwa\UI;

/**
 * Textarea element for multi-line text input.
 *
 * Usage:
 *   UI::textarea()
 *       ->name('message')
 *       ->placeholder('Enter your message')
 *       ->rows(5)
 *       ->required()
 */
class Textarea extends UIElement
{
    protected ?string $name = null;
    protected ?string $value = null;
    protected ?string $placeholder = null;
    protected ?string $id = null;
    protected ?int $rows = null;
    protected ?int $cols = null;
    protected bool $required = false;
    protected bool $disabled = false;
    protected bool $readonly = false;
    protected bool $autofocus = false;
    protected ?int $minLength = null;
    protected ?int $maxLength = null;
    protected ?string $wrap = null;
    protected ?string $autocomplete = null;

    /**
     * Set name attribute.
     */
    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set initial value/content.
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
     * Set number of visible rows.
     */
    public function rows(int $rows): static
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * Set number of visible columns.
     */
    public function cols(int $cols): static
    {
        $this->cols = $cols;
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
     * Set wrap behavior (soft, hard, off).
     */
    public function wrap(string $wrap): static
    {
        $this->wrap = $wrap;
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
     * Disable resizing.
     */
    public function noResize(): static
    {
        $this->addStyle('resize-none', ['resize' => 'none']);
        return $this;
    }

    /**
     * Allow vertical resizing only.
     */
    public function resizeVertical(): static
    {
        $this->addStyle('resize-y', ['resize' => 'vertical']);
        return $this;
    }

    /**
     * Allow horizontal resizing only.
     */
    public function resizeHorizontal(): static
    {
        $this->addStyle('resize-x', ['resize' => 'horizontal']);
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('textarea');

        if ($this->name !== null) {
            $node->attr('name', $this->name);
        }

        if ($this->placeholder !== null) {
            $node->attr('placeholder', $this->placeholder);
        }

        if ($this->id !== null) {
            $node->attr('id', $this->id);
        }

        if ($this->rows !== null) {
            $node->attr('rows', (string)$this->rows);
        }

        if ($this->cols !== null) {
            $node->attr('cols', (string)$this->cols);
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

        if ($this->minLength !== null) {
            $node->attr('minlength', (string)$this->minLength);
        }

        if ($this->maxLength !== null) {
            $node->attr('maxlength', (string)$this->maxLength);
        }

        if ($this->wrap !== null) {
            $node->attr('wrap', $this->wrap);
        }

        if ($this->autocomplete !== null) {
            $node->attr('autocomplete', $this->autocomplete);
        }

        if ($this->value !== null) {
            $node->children($this->value);
        }

        return $node;
    }
}
