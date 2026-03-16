<?php

namespace Spwa\UI;

/**
 * Form element.
 */
class Form extends Container
{
    public function __construct()
    {
        parent::__construct('form');
    }

    protected ?string $action = null;
    protected ?string $method = null;
    protected ?string $enctype = null;
    protected ?string $target = null;
    protected bool $novalidate = false;
    protected ?string $autocomplete = null;

    public function action(string $action): static
    {
        $this->action = $action;
        return $this;
    }

    public function method(string $method): static
    {
        $this->method = $method;
        return $this;
    }

    public function get(): static
    {
        return $this->method('get');
    }

    public function post(): static
    {
        return $this->method('post');
    }

    public function enctype(string $enctype): static
    {
        $this->enctype = $enctype;
        return $this;
    }

    public function multipart(): static
    {
        return $this->enctype('multipart/form-data');
    }

    public function target(string $target): static
    {
        $this->target = $target;
        return $this;
    }

    public function novalidate(bool $novalidate = true): static
    {
        $this->novalidate = $novalidate;
        return $this;
    }

    public function autocomplete(string $value): static
    {
        $this->autocomplete = $value;
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('form');

        if ($this->action !== null) {
            $node->attr('action', $this->action);
        }

        if ($this->method !== null) {
            $node->attr('method', $this->method);
        }

        if ($this->enctype !== null) {
            $node->attr('enctype', $this->enctype);
        }

        if ($this->target !== null) {
            $node->attr('target', $this->target);
        }

        if ($this->novalidate) {
            $node->attr('novalidate', 'novalidate');
        }

        if ($this->autocomplete !== null) {
            $node->attr('autocomplete', $this->autocomplete);
        }

        foreach ($this->children as $child) {
            $node->children($child->build());
        }

        return $node;
    }
}
