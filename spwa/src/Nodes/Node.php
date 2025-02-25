<?php

namespace Spwa\Nodes;

abstract class Node
{
    protected $key = null;
    protected ?array $children = [];
    protected int $index;
    protected ?Node $parent = null;

    /**
     * @param Node[] $children
     */
    public function __construct(array $children = null)
    {
        $this->children = $children;
        if ($children != null) {
            foreach ($children as $i => $child) {
                $child->index = $i;
                $child->parent = $this;
            }
        }
    }

    abstract function compare(Node $node, PatchBuilder $patch): void;

    abstract function renderHtml(): string;

    abstract function find(array $path): ?Node;


    abstract function initialize(?Node $parent, PathInfo $current, StateManager $manager): void;

    abstract function finalize(StateManager $manager): void;
}




