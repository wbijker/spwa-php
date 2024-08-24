<?php

namespace Spwa\Template;

class Event
{
    public string $event;
    public NodePath $path;
    /**
     * @var callable
     */
    public $handler;

    /**
     * @param string $event
     * @param NodePath $path
     * @param callable $handler
     */
    public function __construct(string $event, NodePath $path, callable $handler)
    {
        $this->event = $event;
        $this->path = $path;
        $this->handler = $handler;
    }


}