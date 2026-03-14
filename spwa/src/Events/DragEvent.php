<?php

namespace Spwa\Events;

class DragEvent
{
    /**
     * @param string[] $types
     */
    public function __construct(
        public readonly float   $clientX = 0,
        public readonly float   $clientY = 0,
        public readonly float   $offsetX = 0,
        public readonly float   $offsetY = 0,
        public readonly ?string $text = null,
        public readonly array   $types = [],
        public readonly string  $dropEffect = '',
        public readonly string  $effectAllowed = '',
    ) {}

    public static function from(mixed $data): self
    {
        if (!is_array($data)) return new self();
        return new self(
            clientX: (float)($data['clientX'] ?? 0),
            clientY: (float)($data['clientY'] ?? 0),
            offsetX: (float)($data['offsetX'] ?? 0),
            offsetY: (float)($data['offsetY'] ?? 0),
            text: $data['text'] ?? null,
            types: $data['types'] ?? [],
            dropEffect: (string)($data['dropEffect'] ?? ''),
            effectAllowed: (string)($data['effectAllowed'] ?? ''),
        );
    }
}
