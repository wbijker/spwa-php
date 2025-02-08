<?php

namespace Spwa\Nodes;

use ReflectionClass;
use ReflectionProperty;
use Spwa\Js\JS;

abstract class Component extends Node
{

    function compare(Node $node, PatchBuilder $patch): void
    {
        if ((!($node instanceof Component)) || get_class($node) != get_class($this)) {
            $patch->replace($this, $node);
            return;
        }

        $this->getNode()->compare($node->getNode(), $patch);
    }

    function renderHtml(): string
    {
        return $this->getNode()->renderHtml();
    }

    function initialize(?Node $parent, PathInfo $current, StateManager $manager): void
    {
        $node = $this->getNode();

        $className = basename(str_replace('\\', '/', get_class($this)));

        $this->path = $current->set($className, $className);

        $saved = $manager->restoreState($this->path->keyStr());
        if ($saved != null) {
            $this->restoreState($saved);
        }
        $node->initialize($this, $this->path, $manager);
    }

    function finalize(StateManager $manager): void
    {
        $manager->saveState($this->path->keyStr(), $this->saveState());
        $this->getNode()->finalize($manager);
    }

    private Node $node;

    public function getNode(): Node
    {
        $this->node ??= $this->render();
        return $this->node;
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