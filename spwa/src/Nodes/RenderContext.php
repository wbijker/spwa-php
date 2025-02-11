<?php

namespace Spwa\Nodes;

use Spwa\Js\JS;

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

    private static array $state = [];

    private int $index = 0;

    /**
     * @template T
     * @param T $state
     * @return T
     */
    public function createState($state)
    {
        // read $this->current->keyStr()][$this->index] from state
        // if available call restored
        // else call state->initialize and fill the cache

        self::$state[$this->current->keyStr()][$this->index] = $state;
//        JS::log("Creating state", $this->current->keyStr(), get_class($state));
        JS::log(json_encode(self::$state));

        $this->index++;
        return $state;
    }

}