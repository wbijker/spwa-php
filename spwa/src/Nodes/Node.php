<?php

namespace Spwa\Nodes;

abstract class Node
{
    protected $key = null;
    public ?array $children = [];
    protected int $index = 0;
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

    public function transverse(array $path): ?Node
    {
        $key = array_shift($path);
        if ($key === null)
            return $this;

        return $this->children[$key]?->transverse($path);
    }

    public function path(): array
    {
        $ret = [$this->index];
        // add to the front of the array
        $it = $this->parent;
        while ($it != null) {
            array_unshift($ret, $it->index);
            $it = $it->parent;
        }
        return $ret;
    }

    abstract function compare(Node $node, PatchBuilder $patch): void;

    abstract function renderHtml(): string;

    abstract function find(array $path): ?Node;


    abstract function initialize(?Node $parent, PathInfo $current, StateManager $manager): void;

    abstract function finalize(StateManager $manager): void;
}




