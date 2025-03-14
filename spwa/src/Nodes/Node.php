<?php

namespace Spwa\Nodes;

abstract class Node
{
    public ?PathInfo $path = null;
    protected $key = null;

    abstract function renderHtml(): string;
    abstract function find(array $path): ?Node;


    abstract function initializeAndCompare(?Node $parent, PathInfo $current, StateManager $manager, Node $old, PatchBuilder $patch): void;

    abstract function initialize(?Node $parent, PathInfo $current, StateManager $manager): void;
    abstract function finalize(StateManager $manager): void;
}




