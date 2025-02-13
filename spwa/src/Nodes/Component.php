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
        $this->node = $this->render();
        $this->node->initialize($this, $this->path, $manager);
    }

    function finalize(StateManager $manager): void
    {
        $manager->saveState($this->path->keyStr(), $this->saveState());
        $this->node->finalize($manager);
    }


    private function hasStateAttribute(ReflectionProperty $property): bool
    {
        return count($property->getAttributes(State::class)) > 0;
    }

    public function saveState(): array
    {
        // get all members of the class annotated with #[State]
        $state = [];
        $reflection = new ReflectionClass($this);

        foreach ($reflection->getProperties() as $property) {
            if ($this->hasStateAttribute($property)) {
                $state[$property->getName()] = $property->getValue($this);
            }
        }

        return $state;
    }

    public function restoreState(array $saved): void
    {
        foreach ($saved as $key => $value) {
            // check if the property also has state attribute
            if (property_exists($this, $key) && gettype($this->$key) == gettype($value)) {
                $this->$key = $value;
            }
        }
    }

    abstract function render(): Node;
}