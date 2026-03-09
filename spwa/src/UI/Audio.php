<?php

namespace Spwa\UI;

/**
 * Audio element.
 */
class Audio extends UIElement
{
    protected ?string $src = null;
    protected bool $controls = true;
    protected bool $autoplay = false;
    protected bool $loop = false;
    protected bool $muted = false;
    protected ?string $preload = null;
    protected ?string $crossorigin = null;
    /** @var Source[] */
    protected array $sources = [];

    public function src(string $src): static
    {
        $this->src = $src;
        return $this;
    }

    public function controls(bool $controls = true): static
    {
        $this->controls = $controls;
        return $this;
    }

    public function autoplay(bool $autoplay = true): static
    {
        $this->autoplay = $autoplay;
        return $this;
    }

    public function loop(bool $loop = true): static
    {
        $this->loop = $loop;
        return $this;
    }

    public function muted(bool $muted = true): static
    {
        $this->muted = $muted;
        return $this;
    }

    public function preload(string $preload): static
    {
        $this->preload = $preload;
        return $this;
    }

    public function crossorigin(string $crossorigin): static
    {
        $this->crossorigin = $crossorigin;
        return $this;
    }

    public function sources(Source ...$sources): static
    {
        $this->sources = array_merge($this->sources, $sources);
        return $this;
    }

    public function render(): DomNode
    {
        $node = $this->node('audio');

        if ($this->src !== null) {
            $node->attr('src', $this->src);
        }

        if ($this->controls) {
            $node->attr('controls', 'controls');
        }

        if ($this->autoplay) {
            $node->attr('autoplay', 'autoplay');
        }

        if ($this->loop) {
            $node->attr('loop', 'loop');
        }

        if ($this->muted) {
            $node->attr('muted', 'muted');
        }

        if ($this->preload !== null) {
            $node->attr('preload', $this->preload);
        }

        if ($this->crossorigin !== null) {
            $node->attr('crossorigin', $this->crossorigin);
        }

        foreach ($this->sources as $source) {
            $node->children($source->toNode());
        }

        return $node;
    }
}
