<?php

namespace Spwa\Template;

class PathState
{
    /**
     * @var PathData[]
     */
    private array $dic = [];


    function set(NodePath $path): PathData
    {
        $key = $path->render();
        if (!isset($this->dic[$key])) {
            $this->dic[$key] = new PathData([], null);
        }
        return $this->dic[$key];
    }

    function get(NodePath $path): ?PathData
    {
        return $this->dic[$path->render()];
    }


    function addEvent(Event $event): void
    {
        $item = $this->set($event->path);
        $item->events[] = $event;
    }

    function getEvent(string $event, NodePath $path): ?Event
    {
        $item = $this->get($path);
        if (!$item) {
            return null;
        }
        foreach ($item->events as $e) {
            if ($e->event === $event) {
                return $e;
            }
        }
        return null;
    }

    function getComponent(NodePath $path): ?Component
    {
        $item = $this->get($path);
        if (!$item) {
            return null;
        }
        return $item->component;
    }
}