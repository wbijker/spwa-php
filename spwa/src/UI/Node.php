<?php

namespace Spwa\UI;

/**
 * Base class for DOM nodes.
 */
abstract class Node
{
    protected string|int|null $key = null;

    /** @var int[] Path to this node in the tree */
    protected array $path = [];

    /**
     * Create an element node.
     */
    public static function el(string $tag): TagNode
    {
        return new TagNode($tag);
    }

    /**
     * Create a text node.
     */
    public static function text(string $content): TextNode
    {
        return new TextNode($content);
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
}
