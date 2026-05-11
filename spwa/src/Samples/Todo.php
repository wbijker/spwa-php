<?php

namespace Spwa\Samples;

final class Todo
{
    public function __construct(
        public int $id,
        public string $text,
        public bool $completed,
    ) {}
}
