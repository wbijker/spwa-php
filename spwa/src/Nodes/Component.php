<?php

namespace Spwa\Nodes;


use ReflectionClass;
use ReflectionProperty;
use Spwa\Js\Console;
use Spwa\Js\JS;

abstract class Component extends Node
{
    function initialized(): void {}
    function finalized(): void {}

    function initialPhase(): void {}
    function patchPhase(): void {}

    // rendered node
    public Node $node;

    function initializeAndCompare(?Node $parent, PathInfo $current, StateManager $manager, Node $old, PatchBuilder $patch): void
    {
        if ((!($old instanceof Component)) || get_class($old) != get_class($this)) {
            // initialize everything before rendering
            $this->initialize($this, $current, $manager);
            $patch->replace($this, $old);
            return;
        }

        $this->patchPhase();
        $this->path = $current->setInstance($this->getInstanceName());
        $this->restoreState($manager->restoreState($this->path->keyStr()));
        $this->node = $this->render();
        $this->node->initializeAndCompare($this, $this->path, $manager, $old->node, $patch);
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
        $this->initialPhase();
        $this->path = $current->setInstance($this->getInstanceName());

        $this->restoreState($manager->restoreState($this->path->keyStr()));

        $this->node = $this->render();
        $this->node->initialize($this, $this->path, $manager);
        $this->initialized();
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
        $this->finalized();
        $manager->saveState($this->path->keyStr(), $this->saveState());
        $this->node->finalize($manager);
    }

    abstract function render(): Node;
}

