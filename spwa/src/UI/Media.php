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
