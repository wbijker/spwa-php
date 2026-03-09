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
     * Compare this node with another node and generate patches.
     * @param VNode $parent The parent VNode
     * @param StateManager $manager The state manager
     * @param VNode $other The other VNode to compare with
     * @param Patcher $patcher The patcher to record operations
     */
    public function compare(VNode $parent, StateManager $manager, VNode $other, Patcher $patcher): void
    {
        $this->parent = $parent;
        $this->path = $parent->getPath();

        // If types don't match, replace the node
        if (!$other instanceof static) {
            $patcher->replaceNode($this->path, $this->render($manager, $parent));
            return;
        }

        // Compare the wrapped DOM nodes
        // For now, just replace if they're different instances
        if ($this->domNode !== $other->domNode) {
            $patcher->replaceNode($this->path, $this->render($manager, $parent));
        }
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
