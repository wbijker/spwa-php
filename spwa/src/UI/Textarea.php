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
class Textarea extends UIElementContent
{
    protected ?string $inputName = null;
    protected ?string $inputValue = null;
    protected ?string $inputPlaceholder = null;
    protected ?string $inputId = null;
    protected ?int $inputRows = null;
    protected ?int $inputCols = null;
    protected bool $isRequired = false;
    protected bool $isDisabled = false;
    protected bool $isReadonly = false;
    protected bool $isAutofocus = false;
    protected ?int $inputMinLength = null;
    protected ?int $inputMaxLength = null;
    protected ?string $inputWrap = null;
    protected ?string $inputAutocomplete = null;

    public function __construct()
    {
        parent::__construct('textarea');
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
     * Set initial value/content.
     */
    public function value(string $value): static
    {
        $this->inputValue = $value;
        return $this;
    }

    /**
     * Bind a component property to this textarea's value.
     * The property will be hydrated with the frontend value on each request.
     */
    public function bind(string &$ref): static
    {
        $this->inputValue = $ref;
        $this->dom()->attr('data-bind', 'true');
        $this->dom()->bindRef($ref);
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
     * Set number of visible rows.
     */
    public function rows(int $rows): static
    {
        $this->inputRows = $rows;
        return $this;
    }

    /**
     * Set number of visible columns.
     */
    public function cols(int $cols): static
    {
        $this->inputCols = $cols;
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
     * Set wrap behavior (soft, hard, off).
     */
    public function wrap(string $wrap): static
    {
        $this->inputWrap = $wrap;
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

    /**
     * Render to HTML string.
     */
    public function toHtml(): string
    {
        if ($this->inputName !== null) {
            $this->attr('name', $this->inputName);
        }

        if ($this->inputPlaceholder !== null) {
            $this->attr('placeholder', $this->inputPlaceholder);
        }

        if ($this->inputId !== null) {
            $this->attr('id', $this->inputId);
        }

        if ($this->inputRows !== null) {
            $this->attr('rows', (string)$this->inputRows);
        }

        if ($this->inputCols !== null) {
            $this->attr('cols', (string)$this->inputCols);
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

        if ($this->inputMinLength !== null) {
            $this->attr('minlength', (string)$this->inputMinLength);
        }

        if ($this->inputMaxLength !== null) {
            $this->attr('maxlength', (string)$this->inputMaxLength);
        }

        if ($this->inputWrap !== null) {
            $this->attr('wrap', $this->inputWrap);
        }

        if ($this->inputAutocomplete !== null) {
            $this->attr('autocomplete', $this->inputAutocomplete);
        }

        if ($this->inputValue !== null) {
            $this->content($this->inputValue);
        }

        return parent::toHtml();
    }
}
