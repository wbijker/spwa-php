<?php

namespace Spwa\Events;

class PointerEvent
{
    public function __construct(
        public readonly float  $clientX = 0,
        public readonly float  $clientY = 0,
        public readonly float  $offsetX = 0,
        public readonly float  $offsetY = 0,
        public readonly int    $button = 0,
        public readonly int    $pointerId = 0,
        public readonly string $pointerType = '',
        public readonly float  $pressure = 0,
        public readonly float  $width = 1,
        public readonly float  $height = 1,
        public readonly bool   $altKey = false,
        public readonly bool   $ctrlKey = false,
        public readonly bool   $shiftKey = false,
        public readonly bool   $metaKey = false,
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
            pointerId: (int)($data['pointerId'] ?? 0),
            pointerType: (string)($data['pointerType'] ?? ''),
            pressure: (float)($data['pressure'] ?? 0),
            width: (float)($data['width'] ?? 1),
            height: (float)($data['height'] ?? 1),
            altKey: (bool)($data['altKey'] ?? false),
            ctrlKey: (bool)($data['ctrlKey'] ?? false),
            shiftKey: (bool)($data['shiftKey'] ?? false),
            metaKey: (bool)($data['metaKey'] ?? false),
        );
    }
}
