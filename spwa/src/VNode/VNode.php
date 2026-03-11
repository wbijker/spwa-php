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
     * @param RenderPhase $phase The render phase (Initial or Patch)
     */
    abstract public function render(StateManager $state, ?VNode $parent = null, RenderPhase $phase = RenderPhase::Initial): DomNode;

    /**
     * Get the path to this node.
     * @return int[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * Set the path for this node.
     * @param int[] $path
     */
    public function setPath(array $path): void
    {
        $this->path = $path;
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
