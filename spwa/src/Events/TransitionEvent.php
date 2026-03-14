<?php

namespace Spwa\Events;

class TransitionEvent
{
    public function __construct(
        public readonly string $propertyName = '',
        public readonly float  $elapsedTime = 0,
        public readonly string $pseudoElement = '',
    ) {}

    public static function from(mixed $data): self
    {
        if (!is_array($data)) return new self();
        return new self(
            propertyName: (string)($data['propertyName'] ?? ''),
            elapsedTime: (float)($data['elapsedTime'] ?? 0),
            pseudoElement: (string)($data['pseudoElement'] ?? ''),
        );
    }
}
