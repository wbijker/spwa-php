<?php

namespace Spwa\Nodes;

use Spwa\Js\Console;
use Spwa\Js\JS;

class StateManager
{
    // key dictionary contain state
    private array $state = [];

    function clear(): void
    {
        $this->state = [];
    }

    function unserialize($data): void
    {
        if ($data == null)
            return;

        try {
            $unserialize = unserialize($data);

            if (is_array($unserialize)) {
                $this->state = $unserialize;
            }
        } catch (\Throwable $e) {
            // deserialization failed
            Console::error($e->getMessage());
        }
    }

    function serialize(): string
    {
        // json_encode(
        return serialize($this->state);
    }

    function restoreState(string $key)
    {
        return $this->state[$key] ?? null;
    }

    function saveState(string $key, $state): void
    {
        $this->state[$key] = $state;
    }
}