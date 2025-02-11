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

        $this->state = json_decode($data, true);
    }

    function serialize(): string
    {
        return json_encode($this->state);
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