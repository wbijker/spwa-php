<?php

namespace Spwa\Nodes;


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

        $this->node->compare($node->node, $patch);
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

        $this->restoreState($manager->restoreState($this->path->keyStr()));

        $this->node = $this->render();
        $this->node->initialize($this, $this->path, $manager);
    }

    function restoreState($array): void
    {
        if (empty($array))
            return;

        foreach ($array as $index => $data) {
            $existing = $this->states[$index];

            if ($existing instanceof State) {
                $existing->fromJson($data);
                continue;
            }

            // manual
            foreach ($data as $key => $value) {
                if (property_exists($existing, $key)) {
                    $existing->$key = $value;
                }
            }
        }
    }

    function saveState(): array
    {
        return array_map(function ($state) {
            if ($state instanceof State) {
                return $state->toJson();
            }
            return get_object_vars($state);
        }, $this->states);
    }

    function finalize(StateManager $manager): void
    {
        $manager->saveState($this->path->keyStr(), $this->saveState());
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

