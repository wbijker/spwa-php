<?php

namespace Spwa\Template;

class PathData
{
    /**
     * @var callable[]
     */
    public array $events = [];

    public ?NodeAttributeBind $binding = null;

    public function addEvent(string $event, callable $handler): void
    {
        $this->events[$event] = $handler;
    }
    public function getEvent(string $event): ?callable
    {
        return $this->events[$event];
    }
}