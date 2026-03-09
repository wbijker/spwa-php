<?php

namespace Spwa\VNode;

use Spwa\State\StateManager;
use Spwa\UI\DomNode;

/**
 * A leaf virtual node that wraps a DOM node directly.
 */
class Node extends VNode
{
    /** @var DomNode The wrapped DOM node */
    protected DomNode $domNode;

    /**
     * Create a new Node wrapping a DOM node.
     */
    public function __construct(DomNode $domNode)
    {
        $this->domNode = $domNode;
    }

    /**
     * Render this node to a DOM node.
     * @param StateManager $state The state manager
     * @param VNode|null $parent The parent VNode
     */
    public function render(StateManager $state, ?VNode $parent = null): DomNode
    {
        $this->parent = $parent;
        // Only set path from parent if not already set (e.g., by setPath)
        if (empty($this->path)) {
            $this->path = $parent?->getPath() ?? [];
        }

        return $this->domNode->assignPaths($this->path);
    }

    /**
     * Get the wrapped DOM node.
     */
    public function getDomNode(): DomNode
    {
        return $this->domNode;
    }

    /**
     * Finalize this node. Nodes don't have state, so this is a no-op.
     * @param StateManager $state The state manager
     */
    public function finalize(StateManager $state): void
    {
        // Nodes don't have state to save
    }
}
