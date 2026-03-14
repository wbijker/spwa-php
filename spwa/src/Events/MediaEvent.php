<?php

namespace Spwa\Events;

class MediaEvent
{
    public function __construct(
        public readonly float $currentTime = 0,
        public readonly float $duration = 0,
        public readonly bool  $paused = true,
        public readonly float $volume = 1,
        public readonly bool  $muted = false,
        public readonly float $playbackRate = 1,
        public readonly bool  $ended = false,
        public readonly int   $readyState = 0,
    ) {}

    public static function from(mixed $data): self
    {
        if (!is_array($data)) return new self();
        return new self(
            currentTime: (float)($data['currentTime'] ?? 0),
            duration: (float)($data['duration'] ?? 0),
            paused: (bool)($data['paused'] ?? true),
            volume: (float)($data['volume'] ?? 1),
            muted: (bool)($data['muted'] ?? false),
            playbackRate: (float)($data['playbackRate'] ?? 1),
            ended: (bool)($data['ended'] ?? false),
            readyState: (int)($data['readyState'] ?? 0),
        );
    }
}
