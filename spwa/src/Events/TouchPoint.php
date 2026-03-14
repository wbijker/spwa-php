<?php

namespace Spwa\Events;

class TouchPoint
{
    public function __construct(
        public readonly float $clientX = 0,
        public readonly float $clientY = 0,
        public readonly int   $identifier = 0,
    ) {}

    public static function from(mixed $data): self
    {
        if (!is_array($data)) return new self();
        return new self(
            clientX: (float)($data['clientX'] ?? 0),
            clientY: (float)($data['clientY'] ?? 0),
            identifier: (int)($data['identifier'] ?? 0),
        );
    }
}
