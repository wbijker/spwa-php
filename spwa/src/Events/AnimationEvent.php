<?php

namespace Spwa\Events;

class AnimationEvent
{
    public function __construct(
        public readonly string $animationName = '',
        public readonly float  $elapsedTime = 0,
        public readonly string $pseudoElement = '',
    ) {}

    public static function from(mixed $data): self
    {
        if (!is_array($data)) return new self();
        return new self(
            animationName: (string)($data['animationName'] ?? ''),
            elapsedTime: (float)($data['elapsedTime'] ?? 0),
            pseudoElement: (string)($data['pseudoElement'] ?? ''),
        );
    }
}
