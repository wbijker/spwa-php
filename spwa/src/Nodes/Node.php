<?php

namespace Spwa\Nodes;

abstract class Node
{
    public array $path = [];
    public array $key = [];

    function pathStr(): string
    {
        return implode("|", $this->path);
    }

    function keyStr(): string
    {
        return implode("|", $this->key);
    }

    abstract function compare(Node $node, PatchBuilder $patch): void;

    abstract function renderHtml(): string;

    abstract function initialize(?Node $parent, PathInfo $path, StateManager $manager): void;

    abstract function finalize(StateManager $manager): void;
}




