<?php

namespace Spwa\Template;

class EventListeners
{

    // dictionary[NodePath] of events
    // @var Event[] $events
    private array $events = [];

    function addEvent(Event $event): void
    {
        $key = $this->key($event->event, $event->path);
        $this->events[$key] = $event;
    }

    private function key(string $event, NodePath $path): string
    {
        return $event . $path->render();
    }

    function getEvent(string $event, NodePath $path): ?Event
    {
        $key = $this->key($event, $path);
        return $this->events[$key] ?? null;
    }

    public function debug()
    {
        print_r(array_keys($this->events));
    }
}