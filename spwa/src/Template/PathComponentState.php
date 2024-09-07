<?php

namespace Spwa\Template;

class PathComponentState
{
    public string $name;
    public array $props;

    public function __construct(string $name, array $props)
    {
        $this->name = $name;
        $this->props = $props;
    }
}