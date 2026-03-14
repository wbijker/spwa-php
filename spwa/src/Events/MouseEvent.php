<?php

namespace Spwa\Events;

class MouseEvent
{
    public function __construct(
        public readonly float $clientX = 0,
        public readonly float $clientY = 0,
        public readonly float $offsetX = 0,
        public readonly float $offsetY = 0,
        public readonly int   $button = 0,
        public readonly bool  $altKey = false,
        public readonly bool  $ctrlKey = false,
        public readonly bool  $shiftKey = false,
        public readonly bool  $metaKey = false,
        public readonly ?string $value = null,
    ) {}

    public static function from(mixed $data): self
    {
        if (!is_array($data)) return new self();
        return new self(
            clientX: (float)($data['clientX'] ?? 0),
            clientY: (float)($data['clientY'] ?? 0),
            offsetX: (float)($data['offsetX'] ?? 0),
            offsetY: (float)($data['offsetY'] ?? 0),
            button: (int)($data['button'] ?? 0),
            altKey: (bool)($data['altKey'] ?? false),
            ctrlKey: (bool)($data['ctrlKey'] ?? false),
            shiftKey: (bool)($data['shiftKey'] ?? false),
            metaKey: (bool)($data['metaKey'] ?? false),
            value: $data['value'] ?? null,
        );
    }
}
