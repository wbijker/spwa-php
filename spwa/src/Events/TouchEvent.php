<?php

namespace Spwa\Events;

class TouchEvent
{
    /**
     * @param TouchPoint[] $touches
     * @param TouchPoint[] $changedTouches
     */
    public function __construct(
        public readonly array $touches = [],
        public readonly array $changedTouches = [],
    ) {}

    public static function from(mixed $data): self
    {
        if (!is_array($data)) return new self();
        return new self(
            touches: array_map(
                fn($t) => TouchPoint::from($t),
                $data['touches'] ?? []
            ),
            changedTouches: array_map(
                fn($t) => TouchPoint::from($t),
                $data['changedTouches'] ?? []
            ),
        );
    }
}
