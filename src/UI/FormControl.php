<?php

namespace BrickPHP\UI;

/**
 * Shared form-field state and setters for the text-entry controls (Input,
 * Textarea). These elements have different parents (UIElement vs
 * UIElementContent), so the common surface lives in a trait rather than a
 * base class. Each consuming class declares its own applyAttributes(), which
 * reads the properties this trait provides.
 */
trait FormControl
{
    protected ?string $inputName = null;
    protected ?string $inputValue = null;
    protected ?string $inputPlaceholder = null;
    protected ?string $inputId = null;
    protected bool $isRequired = false;
    protected bool $isDisabled = false;
    protected bool $isReadonly = false;
    protected bool $isAutofocus = false;
    protected ?int $inputMinLength = null;
    protected ?int $inputMaxLength = null;
    protected ?string $inputAutocomplete = null;

    /** Set the name attribute. */
    public function name(string $name): static
    {
        $this->inputName = $name;
        return $this;
    }

    /** Set the value (initial content). */
    public function value(string $value): static
    {
        $this->inputValue = $value;
        return $this;
    }

    /**
     * Bind a component property to this field's value. The property is
     * hydrated with the frontend value on each request.
     */
    public function bind(string &$ref): static
    {
        $this->inputValue = $ref;
        $this->dom()->bindRef($ref);
        return $this;
    }

    /** Set placeholder text. */
    public function placeholder(string $placeholder): static
    {
        $this->inputPlaceholder = $placeholder;
        return $this;
    }

    /** Set the id attribute. */
    public function id(string $id): static
    {
        $this->inputId = $id;
        return $this;
    }

    /** Mark as required. */
    public function required(bool $required = true): static
    {
        $this->isRequired = $required;
        return $this;
    }

    /** Mark as disabled. */
    public function disabled(bool $disabled = true): static
    {
        $this->isDisabled = $disabled;
        return $this;
    }

    /** Mark as readonly. */
    public function readonly(bool $readonly = true): static
    {
        $this->isReadonly = $readonly;
        return $this;
    }

    /** Set autofocus. */
    public function autofocus(bool $autofocus = true): static
    {
        $this->isAutofocus = $autofocus;
        return $this;
    }

    /** Set autocomplete behavior. */
    public function autocomplete(string $value): static
    {
        $this->inputAutocomplete = $value;
        return $this;
    }

    /** Set minimum length. */
    public function minLength(int $length): static
    {
        $this->inputMinLength = $length;
        return $this;
    }

    /** Set maximum length. */
    public function maxLength(int $length): static
    {
        $this->inputMaxLength = $length;
        return $this;
    }
}
