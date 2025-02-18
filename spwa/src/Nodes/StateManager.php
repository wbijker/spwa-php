<?php

namespace Spwa\Nodes;

use Spwa\Js\JS;

class StateManager
{
    // key dictionary contain state
    private array $state = [];

    private array $events = [];

    function unserialize($data): void
    {
        if ($data == null)
            return;

        $unserialize = @unserialize($data);
        if (is_array($unserialize)) {
            $this->state = $unserialize;
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

    function bindEvent(Node $owner, string $event, callable $callback): void
    {
        $this->events[$owner->path->pathStr()][$event] = $callback;
    }

    function triggerEvent(string $path, string $event): void
    {
        $handler = $this->events[$path][$event] ?? null;

        if (is_callable($handler)) {
            $handler();
        }
    }
}