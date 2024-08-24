<?php

namespace Spwa\Template;

class NodeAttributeEvent extends NodeAttribute
{
    public string $name;
    /**
     * @var callable
     */
    public $handler;

    public function __construct(string $name, callable $handler)
    {
        $this->name = $name;
        $this->handler = $handler;
    }
}