<?php

namespace Spwa\Events;

class InputEvent
{
    public function __construct(
        public readonly ?string $value = null,
        public readonly ?bool   $checked = null,
    ) {}

    public static function from(mixed $data): self
    {
        if (is_string($data)) {
            return new self(value: $data);
        }
        if (is_array($data)) {
            return new self(
                value: $data['value'] ?? null,
                checked: isset($data['checked']) ? (bool)$data['checked'] : null,
            );
        }
        return new self();
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
