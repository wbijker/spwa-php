<?php

namespace Spwa\UI;

/**
 * Audio element. Shared media attributes (src, controls, autoplay, loop,
 * muted, preload, crossorigin, sources) and playback events live on Media.
 */
class Audio extends Media
{
    public function __construct()
    {
        parent::__construct('audio');
    }

    protected function applyAttributes(): void
    {
        $this->applyMediaAttributes();
    }
}
