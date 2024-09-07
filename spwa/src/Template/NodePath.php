<?php

namespace Spwa\Template;


// Represent the DOM transversal path to get to this element
// Transversal through childNodes including text nodes
// Path [0, 1, 2] will be: node at document.body.childNodes[0].childNodes[1].childNodes[2]
class NodePath
{
    /**
     * @var int[]
     */
    public array $path;

    public function __construct(array $path)
    {
        $this->path = $path;
    }

    static function root(): NodePath
    {
        return new NodePath([0]);
    }

    public function addClone(int $index): NodePath
    {
        // creates a copy with the new element added
        $add = $this->path;
        $add[] = $index;
        return new NodePath($add);
    }

    public function next(int $inc): NodePath {
        $path = $this->path;
        $path[count($path) - 1] += $inc;
        return new NodePath($path);
    }

    public function render(): string
    {
        return "[".implode(",", $this->path)."]";
    }
}