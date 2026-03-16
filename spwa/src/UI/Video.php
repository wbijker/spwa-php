<?php

namespace Spwa\UI;

/**
 * Video element.
 */
class Video extends UIElement
{
    public function __construct()
    {
        parent::__construct('video');
    }

    protected ?string $src = null;
    protected ?string $poster = null;
    protected bool $controls = true;
    protected bool $autoplay = false;
    protected bool $loop = false;
    protected bool $muted = false;
    protected bool $playsinline = false;
    protected ?string $preload = null;
    protected ?string $crossorigin = null;
    /** @var Source[] */
    protected array $sources = [];
    /** @var Track[] */
    protected array $tracks = [];

    public function src(string $src): static
    {
        $this->src = $src;
        return $this;
    }

    public function poster(string $poster): static
    {
        $this->poster = $poster;
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

    public function playsinline(bool $playsinline = true): static
    {
        $this->playsinline = $playsinline;
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

    public function tracks(Track ...$tracks): static
    {
        $this->tracks = array_merge($this->tracks, $tracks);
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('video');

        if ($this->src !== null) {
            $node->attr('src', $this->src);
        }

        if ($this->poster !== null) {
            $node->attr('poster', $this->poster);
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

        if ($this->playsinline) {
            $node->attr('playsinline', 'playsinline');
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

        foreach ($this->tracks as $track) {
            $node->children($track->toNode());
        }

        return $node;
    }
}
