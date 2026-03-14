<?php

namespace Spwa\Events;

class ScrollEvent
{
    public function __construct(
        public readonly float $scrollTop = 0,
        public readonly float $scrollLeft = 0,
        public readonly float $scrollHeight = 0,
        public readonly float $scrollWidth = 0,
    ) {}

    public static function from(mixed $data): self
    {
        if (!is_array($data)) return new self();
        return new self(
            scrollTop: (float)($data['scrollTop'] ?? 0),
            scrollLeft: (float)($data['scrollLeft'] ?? 0),
            scrollHeight: (float)($data['scrollHeight'] ?? 0),
            scrollWidth: (float)($data['scrollWidth'] ?? 0),
        );
    }
}
