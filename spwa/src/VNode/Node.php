<?php

namespace Spwa\VNode;

use Spwa\UI\DomNode;

/**
 * A leaf virtual node that wraps a DOM node directly.
 */
class Node extends VNode
{
    /** @var DomNode The wrapped DOM node */
    private DomNode $domNode;

    /**
     * Create a new Node wrapping a DOM node.
     */
    public function __construct(DomNode $domNode)
    {
        $this->domNode = $domNode;
    }

    /**
     * Render this node to a DOM node.
     * @param VNode|null $parent The parent VNode
     */
    public function render(?VNode $parent = null): DomNode
    {
        $this->parent = $parent;
        $this->path = $parent?->getPath() ?? [];

        return $this->domNode->assignPaths($this->path);
    }

    /**
     * Get the wrapped DOM node.
     */
    public function getDomNode(): DomNode
    {
        return $this->domNode;
    }
}
