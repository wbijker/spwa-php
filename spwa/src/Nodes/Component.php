<?php

namespace Spwa\Nodes;

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

    function initialize(?Node $parent, PathInfo $path, StateManager $manager): void
    {
        $path->set($this, $parent, true);

        $saved = $manager->restoreState($this->keyStr());
        if ($saved != null) {
            $this->restoreState($saved);
        }
        $this->getNode()->initialize($this, new PathInfo(0, get_class($this)), $manager);
    }

    function finalize(StateManager $manager): void
    {
        $manager->saveState($this->keyStr(), $this->saveState());
        $this->getNode()->finalize($manager);
    }

    private Node $node;

    public function getNode(): Node
    {
        $this->node ??= $this->render();
        return $this->node;
    }

    public function saveState(): array
    {
        $vars = get_object_vars($this);
        // remove members living in parent Node
        return array_diff_key($vars, array_flip(['path', 'key', 'node']));
    }

    public function restoreState(array $saved): void
    {
        foreach ($saved as $key => $value) {
            if (property_exists($this, $key) && gettype($this->$key) == gettype($value)) {
                $this->$key = $value;
            }
        }
    }

    abstract function render(): Node;
}