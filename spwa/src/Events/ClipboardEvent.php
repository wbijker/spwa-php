<?php

namespace Spwa\Events;

class ClipboardEvent
{
    public function __construct(
        public readonly ?string $text = null,
    ) {}

    public static function from(mixed $data): self
    {
        if (!is_array($data)) return new self();
        return new self(
            text: $data['text'] ?? null,
        );
    }
}
