<?php

namespace Spwa\Template;

class NodePath
{
    /**
     * @var int[]
     */
    private array $path;

    private function __construct(array $path)
    {
        $this->path = $path;
    }

    public static function empty(): NodePath
    {
        return new NodePath([]);
    }

    public function add(int $index): NodePath
    {
        // creates a copy with the new element added
        $add = $this->path;
        $add[] = $index;
        return new NodePath($add);
    }
}