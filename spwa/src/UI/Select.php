<?php

namespace Spwa\UI;

/**
 * Select dropdown element.
 */
class Select extends UIElement
{
    public function __construct()
    {
        parent::__construct('select');
    }

    protected ?string $name = null;
    protected ?string $id = null;
    protected bool $required = false;
    protected bool $disabled = false;
    protected bool $multiple = false;
    protected ?int $size = null;
    protected ?string $autocomplete = null;
    /** @var (Option|Optgroup)[] */
    protected array $options = [];

    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function id(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;
        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;
        return $this;
    }

    public function size(int $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function autocomplete(string $value): static
    {
        $this->autocomplete = $value;
        return $this;
    }

    /**
     * Bind a component property to this select's value.
     * The property will be hydrated with the frontend value on each request.
     */
    public function bind(string &$ref): static
    {
        $this->dom()->attr('data-bind', 'true');
        $this->dom()->bindRef($ref);
        return $this;
    }

    public function options(Option|Optgroup ...$options): static
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('select');

        if ($this->name !== null) {
            $node->attr('name', $this->name);
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

        if ($this->multiple) {
            $node->attr('multiple', 'multiple');
        }

        if ($this->size !== null) {
            $node->attr('size', (string)$this->size);
        }

        if ($this->autocomplete !== null) {
            $node->attr('autocomplete', $this->autocomplete);
        }

        foreach ($this->options as $option) {
            $node->children($option->toNode());
        }

        return $node;
    }
}
