<?php

namespace Spwa\Template;

class PathData
{
    /**
     * @var callable[]
     */
    public array $events;

    // component instance
    public ?Component $component;

    public function __construct()
    {
        $this->events = [];
        $this->component = null;
    }

    public function addEvent(string $event, callable $handler): void
    {
        $this->events[$event] = $handler;
    }

    public function getEvent(string $event): ?callable
    {
        return $this->events[$event];
    }

}