<?php

namespace BrickPHP\UI;

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
    use FormControl;

    protected ?int $inputRows = null;
    protected ?int $inputCols = null;
    protected ?string $inputWrap = null;

    public function __construct()
    {
        parent::__construct('textarea');
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

    // noResize / resizeX / resizeY now live on UIElement (they were duplicates here).
    protected function applyAttributes(): void
    {
        $this->children = [];

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
    }
}
