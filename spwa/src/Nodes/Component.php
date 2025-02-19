<?php

namespace Spwa\Nodes;


use ReflectionClass;
use ReflectionProperty;


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

    function find(array $path): ?Node
    {
        return $this->node->find($path);
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

    private function getStateProperties(): array
    {
        $props = [];
        $reflection = new ReflectionClass($this);
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE) as $property) {
            if (!empty($property->getAttributes(State::class))) {
                $props[$property->getName()] = $property;
            }
        }
        return $props;
    }

    function restoreState($array): void
    {
        if (empty($array))
            return;

        foreach ($this->getStateProperties() as $name => $prop) {
            $existing = $prop->getValue($this);
            // if the property is null we cannot infer the type
            if ($existing == null || gettype($existing) == gettype($array[$name])) {
                $prop->setValue($this, $array[$name]);
            }
        }
    }

    private function saveState(): array
    {
        return array_map(fn($prop) => $prop->getValue($this), $this->getStateProperties());
    }

    function finalize(StateManager $manager): void
    {
        $manager->saveState($this->path->keyStr(), $this->saveState());
        $this->node->finalize($manager);
    }

    abstract function render(): Node;
}

