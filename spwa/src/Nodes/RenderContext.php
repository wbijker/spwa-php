<?php

namespace Spwa\Nodes;

class RenderContext
{

    public ?Node $parent;
    public PathInfo $current;
    public StateManager $manager;

    /**
     * @param ?Node $parent
     * @param PathInfo $current
     * @param StateManager $manager
     */
    public function __construct(?Node $parent, PathInfo $current, StateManager $manager)
    {
        $this->parent = $parent;
        $this->current = $current;
        $this->manager = $manager;
    }

    public function next(Node $parent, PathInfo $current,): RenderContext
    {
        return new RenderContext($parent, $current, $this->manager);
    }


    /**
     * @template T
     * @param T $state
     * @return T
     */
    public function createState($state)
    {
        return $state;
    }

}