<?php

namespace Spwa\Events;

class WheelEvent
{
    public function __construct(
        public readonly float $deltaX = 0,
        public readonly float $deltaY = 0,
        public readonly float $deltaZ = 0,
        public readonly int   $deltaMode = 0,
        public readonly float $clientX = 0,
        public readonly float $clientY = 0,
    ) {}

    public static function from(mixed $data): self
    {
        if (!is_array($data)) return new self();
        return new self(
            deltaX: (float)($data['deltaX'] ?? 0),
            deltaY: (float)($data['deltaY'] ?? 0),
            deltaZ: (float)($data['deltaZ'] ?? 0),
            deltaMode: (int)($data['deltaMode'] ?? 0),
            clientX: (float)($data['clientX'] ?? 0),
            clientY: (float)($data['clientY'] ?? 0),
        );
    }
}
