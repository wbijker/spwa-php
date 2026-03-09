<?php

namespace Spwa\VNode;

use Spwa\UI\DomNode;

/**
 * A component virtual node that can have state and lifecycle.
 */
abstract class Component extends VNode
{
    /**
     * Build the component's virtual node tree.
     * Override this method to define the component's structure.
     */
    abstract protected function build(): VNode;

    /**
     * Render this component to a DOM node.
     * @param VNode|null $parent The parent VNode
     */
    public function render(?VNode $parent = null): DomNode
    {
        $this->parent = $parent;
        $this->path = $parent?->getPath() ?? [];

        $child = $this->build();

        return $child->render($this);
    }
}
