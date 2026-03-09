<?php

namespace Spwa\VNode;

use Spwa\State\StateManager;
use Spwa\UI\DomNode;

/**
 * Abstract base class for virtual nodes.
 */
abstract class VNode
{
    /** @var int[] Path to this node in the tree */
    protected array $path = [];

    /** @var VNode|null Parent node */
    protected ?VNode $parent = null;

    /**
     * Render this virtual node to a DOM node.
     * @param StateManager $state The state manager
     * @param VNode|null $parent The parent VNode
     */
    abstract public function render(StateManager $state, ?VNode $parent = null): DomNode;

    /**
     * Compare this node with another node and generate patches.
     * @param VNode $parent The parent VNode
     * @param StateManager $manager The state manager
     * @param VNode $other The other VNode to compare with
     * @param Patcher $patcher The patcher to record operations
     */
    abstract public function compare(VNode $parent, StateManager $manager, VNode $other, Patcher $patcher): void;

    /**
     * Get the path to this node.
     * @return int[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * Get the parent node.
     */
    public function getParent(): ?VNode
    {
        return $this->parent;
    }

    /**
     * Finalize this node, saving any state.
     * @param StateManager $state The state manager
     */
    abstract public function finalize(StateManager $state): void;
}
