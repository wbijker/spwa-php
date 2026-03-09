<?php

namespace Spwa\UI;

use Spwa\VNode\Patcher;

/**
 * Base class for DOM nodes.
 */
abstract class DomNode
{
    protected string|int|null $key = null;

    /** @var int[] Path to this node in the tree */
    protected array $path = [];

    /**
     * Create an element node.
     */
    public static function el(string $tag): TagDomNode
    {
        return new TagDomNode($tag);
    }

    /**
     * Create a text node.
     */
    public static function text(string $content): TextDomNode
    {
        return new TextDomNode($content);
    }

    /**
     * Set a unique key for this node.
     */
    public function key(string|int $key): static
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get the node key.
     */
    public function getKey(): string|int|null
    {
        return $this->key;
    }

    /**
     * Get the path to this node.
     * @return int[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * Assign paths to this node and all descendants.
     * Call this on the root node to assign paths to the entire tree.
     *
     * @param int[] $parentPath The path of the parent node
     * @return static
     */
    public function assignPaths(array $parentPath = []): static
    {
        $this->path = $parentPath;
        $this->assignChildPaths();
        return $this;
    }

    /**
     * Assign paths to child nodes.
     * Override in subclasses that have children.
     */
    protected function assignChildPaths(): void
    {
        // Base implementation does nothing (TextNode has no children)
    }

    /**
     * Collect all styles from this node and descendants.
     * @return array<string, array<string, string>>
     */
    abstract public function collectStyles(): array;

    /**
     * Render to HTML string.
     */
    abstract public function toHtml(): string;

    /**
     * Convert to string (alias for toHtml).
     */
    public function __toString(): string
    {
        return $this->toHtml();
    }

    /**
     * Find a node by its path.
     * @param int[] $targetPath
     * @return DomNode|null
     */
    public function findByPath(array $targetPath): ?DomNode
    {
        if ($this->path === $targetPath) {
            return $this;
        }
        return null;
    }

    /**
     * Execute an event handler if it exists.
     * @param string $event
     * @return bool Whether the event was handled
     */
    public function executeEvent(string $event): bool
    {
        return false;
    }

    /**
     * Compare this node with another and generate patches.
     * @param DomNode $other The other node to compare with
     * @param Patcher $patcher The patcher to record operations
     */
    abstract public function compare(DomNode $other, Patcher $patcher): void;
}
