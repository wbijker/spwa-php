<?php

namespace Spwa\Nodes;

abstract class Node
{
    public ?PathInfo $path = null;
    protected $key = null;

    abstract function compare(Node $node, PatchBuilder $patch): void;

    abstract function finalize(StateManager $manager): void;

    abstract function renderHtml(RenderContext $context): string;

//    abstract function initialize(?Node $parent, PathInfo $current, StateManager $manager): void;
}




