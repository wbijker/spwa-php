<?php

namespace Spwa\Events;

class KeyboardEvent
{
    public function __construct(
        public readonly string $key = '',
        public readonly string $code = '',
        public readonly bool   $altKey = false,
        public readonly bool   $ctrlKey = false,
        public readonly bool   $shiftKey = false,
        public readonly bool   $metaKey = false,
        public readonly bool   $repeat = false,
    ) {}

    public static function from(mixed $data): self
    {
        if (!is_array($data)) return new self();
        return new self(
            key: (string)($data['key'] ?? ''),
            code: (string)($data['code'] ?? ''),
            altKey: (bool)($data['altKey'] ?? false),
            ctrlKey: (bool)($data['ctrlKey'] ?? false),
            shiftKey: (bool)($data['shiftKey'] ?? false),
            metaKey: (bool)($data['metaKey'] ?? false),
            repeat: (bool)($data['repeat'] ?? false),
        );
    }
}
