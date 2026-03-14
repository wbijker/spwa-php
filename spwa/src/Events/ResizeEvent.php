<?php

namespace Spwa\Events;

class ResizeEvent
{
    public function __construct(
        public readonly float $width = 0,
        public readonly float $height = 0,
    ) {}

    public static function from(mixed $data): self
    {
        if (!is_array($data)) return new self();
        return new self(
            width: (float)($data['width'] ?? 0),
            height: (float)($data['height'] ?? 0),
        );
    }
}
