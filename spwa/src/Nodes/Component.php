<?php

namespace Spwa\Nodes;

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
        $this->path = $current;
//        $this->path = $path->addChild(get_class($this));
        // a component always has a root node
//        $this->getNode()->initialize($this, $this->path , $manager);

//        $this->path = $path->down(get_class($this));

        //  $path->set($this, $parent, true);
//        $saved = $manager->restoreState($this->keyStr());
//        if ($saved != null) {
//            $this->restoreState($saved);
//        }

        // new PathInfo($path->domIndex, get_class($this))
        $this->getNode()->initialize($this, $this->path, $manager);
    }

    function finalize(StateManager $manager): void
    {
//        $manager->saveState($this->path->keyStr(), $this->saveState());
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
        JS::log('Saving state ', $vars);
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