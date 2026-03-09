<?php

namespace Spwa\UI;

/**
 * Iframe element.
 */
class Iframe extends UIElement
{
    protected ?string $src = null;
    protected ?string $srcdoc = null;
    protected ?string $name = null;
    protected ?string $sandbox = null;
    protected ?string $allow = null;
    protected bool $allowfullscreen = false;
    protected ?string $loading = null;
    protected ?string $referrerpolicy = null;

    public function src(string $src): static
    {
        $this->src = $src;
        return $this;
    }

    public function srcdoc(string $srcdoc): static
    {
        $this->srcdoc = $srcdoc;
        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function sandbox(?string $sandbox = ''): static
    {
        $this->sandbox = $sandbox;
        return $this;
    }

    public function allow(string $allow): static
    {
        $this->allow = $allow;
        return $this;
    }

    public function allowfullscreen(bool $allowfullscreen = true): static
    {
        $this->allowfullscreen = $allowfullscreen;
        return $this;
    }

    public function loading(string $loading): static
    {
        $this->loading = $loading;
        return $this;
    }

    public function lazy(): static
    {
        return $this->loading('lazy');
    }

    public function referrerpolicy(string $policy): static
    {
        $this->referrerpolicy = $policy;
        return $this;
    }

    public function render(): DomNode
    {
        $node = $this->node('iframe');

        if ($this->src !== null) {
            $node->attr('src', $this->src);
        }

        if ($this->srcdoc !== null) {
            $node->attr('srcdoc', $this->srcdoc);
        }

        if ($this->name !== null) {
            $node->attr('name', $this->name);
        }

        if ($this->sandbox !== null) {
            $node->attr('sandbox', $this->sandbox);
        }

        if ($this->allow !== null) {
            $node->attr('allow', $this->allow);
        }

        if ($this->allowfullscreen) {
            $node->attr('allowfullscreen', 'allowfullscreen');
        }

        if ($this->loading !== null) {
            $node->attr('loading', $this->loading);
        }

        if ($this->referrerpolicy !== null) {
            $node->attr('referrerpolicy', $this->referrerpolicy);
        }

        return $node;
    }
}
