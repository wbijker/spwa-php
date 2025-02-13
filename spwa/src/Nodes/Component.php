<?php

namespace Spwa\Nodes;

use ReflectionClass;
use ReflectionProperty;
use Spwa\Js\JS;


abstract class Component extends Node
{
    // rendered node
    public Node $node;

    function compare(Node $node, PatchBuilder $patch): void
    {
        if ((!($node instanceof Component)) || get_class($node) != get_class($this)) {
            $patch->replace($this, $node);
            return;
        }

        $this->node->compare($node, $patch);
    }

    private function getInstanceName(): string
    {
        return basename(str_replace('\\', '/', get_class($this)));
    }

    function renderHtml(): string
    {
        return $this->node->renderHtml();
    }

    function initialize(?Node $parent, PathInfo $current, StateManager $manager): void
    {
        $instance = $this->getInstanceName();
        $this->path = $current->set($instance, $instance);

        $states = $manager->restoreState($this->path->keyStr());

        if ($states != null && is_array($states)) {
            foreach ($this->states as $index => $state) {
                if (gettype($state) == gettype($states[$index])) {
//                    $this->states[$index] = $state;
//                    JS::log("State restored", $index, $state);
                }
            }
        }

        $this->node = $this->render();
        $this->node->initialize($this, $this->path, $manager);
    }

    function finalize(StateManager $manager): void
    {
        $manager->saveState($this->path->keyStr(), $this->states);
        $this->node->finalize($manager);
    }

    public array $states = [];

    /**
     * @template T
     * @param T $instance
     * @return T
     */
    public function createState($instance)
    {
        $this->states[] = $instance;
        return $instance;
    }

    abstract function render(): Node;
}