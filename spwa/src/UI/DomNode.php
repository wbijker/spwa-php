<?php

namespace Spwa\UI;

use Spwa\UI\Css\CssStyle;
use Spwa\VNode\Patcher;

/**
 * Base class for DOM nodes.
 */
abstract class DomNode
{
    /** @var int[] Path to this node in the tree */
    protected array $path = [];

    /** @var bool Whether this node is part of the managed SPWA tree */
    protected bool $managed = false;

    /** @var string|null Key for efficient list diffing */
    protected ?string $key = null;

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
     * Get the path to this node.
     * @return int[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    public function getKey(): ?string
    {
        return $this->key;
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
        $this->managed = true;
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
     * Collect CssStyle objects from this node and descendants.
     * @return CssStyle[]
     */
    public function collectCssStyles(): array
    {
        return [];
    }

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
     * @param string $event The event name
     * @param mixed $state The state manager for finalizing the owner component
     * @param mixed $value Event data extracted by the client (type varies by event)
     * @return bool Whether the event was handled
     */
    public function executeEvent(string $event, mixed $state = null, mixed $value = null): bool
    {
        return false;
    }

    /**
     * Compare this node with another and generate patches.
     * @param DomNode $other The other node to compare with
     * @param Patcher $patcher The patcher to record operations
     */
    abstract public function compare(DomNode $other, Patcher $patcher): void;

    /**
     * Count the total number of DOM nodes in this subtree (including this node).
     */
    public function countNodes(): int
    {
        return 1;
    }
}
