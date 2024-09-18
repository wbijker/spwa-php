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

    static function empty(): NodePath
    {
        return new NodePath([]);
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

    /*
     * nibbles
     * 1 xxx (0 - 7) (0.5 bytes)
     * 01 xx xxxx (7 - 31) (1 bytes)
     * 001 x xxxx xxxx (31 - 127) (1.5 bytes)
     * 0001 xxxx xxxx xxxx (127 - 511) (2 bytes)
     * 0000 1xxx (variable length, up to 7 bytes)
     */
}