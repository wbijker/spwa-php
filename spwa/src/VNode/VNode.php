<?php

namespace Spwa\VNode;

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
     * @param VNode|null $parent The parent VNode
     */
    abstract public function render(?VNode $parent = null): DomNode;

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
}
