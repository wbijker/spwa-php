<?php

namespace Spwa\Nodes;

abstract class Node
{
    public ?PathInfo $path = null;

    abstract function compare(Node $node, PatchBuilder $patch): void;

    abstract function renderHtml(): string;


    abstract function initialize(?Node $parent, PathInfo $current, StateManager $manager): void;

    abstract function finalize(StateManager $manager): void;
}




