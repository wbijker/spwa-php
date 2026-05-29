<?php

namespace Spwa\UI;

use Spwa\Events\EventPhase;
use Spwa\Events\Events;
use Spwa\Events\MediaEvent;

/**
 * Base for media elements (audio / video). Carries the media playback
 * events, which only make sense on a media element and so live here rather
 * than on every UIElement.
 */
abstract class Media extends UIElement
{
    // ============================================================
    // Shared media attributes (audio + video)
    // ============================================================

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

    /**
     * Apply the shared media attributes and <source> children to this
     * element's DOM node. Subclass applyAttributes() calls this, then adds
     * its own element-specific attributes (e.g. poster, <track>). Children
     * are reset first so repeated renders stay idempotent.
     */
    protected function applyMediaAttributes(): void
    {
        $node = $this->dom();
        $node->clearChildren();

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
    }

    // ============================================================
    // Media playback events
    // ============================================================

    /** @param callable(MediaEvent): void $callback */
    public function onPlay(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::PLAY, $callback, null, null, $phase);
        return $this;
    }

    /** @param callable(MediaEvent): void $callback */
    public function onPause(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::PAUSE, $callback, null, null, $phase);
        return $this;
    }

    /** @param callable(MediaEvent): void $callback */
    public function onEnded(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::ENDED, $callback, null, null, $phase);
        return $this;
    }

    /** @param callable(MediaEvent): void $callback */
    public function onTimeUpdate(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::TIME_UPDATE, $callback, null, null, $phase);
        return $this;
    }

    /** @param callable(MediaEvent): void $callback */
    public function onVolumeChange(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::VOLUME_CHANGE, $callback, null, null, $phase);
        return $this;
    }

    /** @param callable(MediaEvent): void $callback */
    public function onSeeking(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::SEEKING, $callback, null, null, $phase);
        return $this;
    }

    /** @param callable(MediaEvent): void $callback */
    public function onSeeked(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::SEEKED, $callback, null, null, $phase);
        return $this;
    }

    /** @param callable(MediaEvent): void $callback */
    public function onLoadedData(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::LOADED_DATA, $callback, null, null, $phase);
        return $this;
    }

    /** @param callable(MediaEvent): void $callback */
    public function onLoadedMetadata(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::LOADED_METADATA, $callback, null, null, $phase);
        return $this;
    }

    /** @param callable(MediaEvent): void $callback */
    public function onCanPlay(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::CAN_PLAY, $callback, null, null, $phase);
        return $this;
    }

    /** @param callable(MediaEvent): void $callback */
    public function onCanPlayThrough(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::CAN_PLAY_THROUGH, $callback, null, null, $phase);
        return $this;
    }

    /** @param callable(MediaEvent): void $callback */
    public function onWaiting(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::WAITING, $callback, null, null, $phase);
        return $this;
    }

    /** @param callable(MediaEvent): void $callback */
    public function onPlaying(callable $callback, EventPhase $phase = EventPhase::Bubble): static
    {
        $this->dom()->on(Events::PLAYING, $callback, null, null, $phase);
        return $this;
    }
}
