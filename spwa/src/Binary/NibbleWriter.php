<?php

namespace Spwa\Binary;

class NibbleWriter
{
    private array $stream = [];
    private int $buffer = 0;
    private bool $first = false;

    function write(int $nibble): void
    {
        if ($this->first) {
            // one full byte
            $this->stream[] = $this->buffer | $nibble;
            $this->buffer = 0;
            $this->first = false;
            return;
        }

        $this->buffer = $nibble << 4;
        $this->first = true;
    }

    function flush(): array
    {
        if ($this->first) {
            $this->stream[] = $this->buffer;
        }
        return $this->stream;
    }
}

class NibbleReader
{
    private array $stream;
    private int $index = 0;
    private int $buffer = 0;
    private bool $first = true;

    function __construct(array $stream)
    {
        $this->stream = $stream;
    }

    function read(): int
    {
        // read 1st nibble
        if ($this->first) {
            $this->buffer = $this->stream[$this->index];
            $this->index++;
            $this->first = false;
            return $this->buffer << 4;
        }

        // read 2nd nibble
        $this->first = true;
        return $this->buffer & 0x0f;
    }

    function eof(): bool
    {
        return $this->index >= count($this->stream) && $this->first;
    }
}