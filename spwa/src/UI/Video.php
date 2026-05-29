<?php

namespace Spwa\UI;

/**
 * Video element. Shared media attributes (src, controls, autoplay, loop,
 * muted, preload, crossorigin, sources) and playback events live on Media;
 * this class adds the video-only poster, playsinline, and <track> support.
 */
class Video extends Media
{
    protected ?string $poster = null;
    protected bool $playsinline = false;
    /** @var Track[] */
    protected array $tracks = [];

    public function __construct()
    {
        parent::__construct('video');
    }

    public function poster(string $poster): static
    {
        $this->poster = $poster;
        return $this;
    }

    public function playsinline(bool $playsinline = true): static
    {
        $this->playsinline = $playsinline;
        return $this;
    }

    public function tracks(Track ...$tracks): static
    {
        $this->tracks = array_merge($this->tracks, $tracks);
        return $this;
    }

    protected function applyAttributes(): void
    {
        $this->applyMediaAttributes();
        $node = $this->dom();

        if ($this->poster !== null) {
            $node->attr('poster', $this->poster);
        }
        if ($this->playsinline) {
            $node->attr('playsinline', 'playsinline');
        }
        foreach ($this->tracks as $track) {
            $node->children($track->toNode());
        }
    }
}
