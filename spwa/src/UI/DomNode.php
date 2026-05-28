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
     * Force-patch flag. When true, the diff emits patches for this node
     * (and — for elements — all of its attributes, classes, text, and
     * descendants) regardless of whether OLD and NEW are equal. Used to
     * keep the frontend DOM aligned with values that the server-vs-server
     * diff can't detect changes in (time, randomness, externally-driven
     * derivations).
     */
    protected bool $invalidated = false;

    /**
     * Skip-diff flag. When true, the diff bails out at this node — no
     * attributes, classes, text, or children are compared, and no patches
     * are emitted for this subtree. The frontend DOM keeps whatever it had
     * from the previous render. Use for truly static UI (headers, logos,
     * decorative content) to avoid traversal cost.
     */
    protected bool $frozen = false;

    public function setInvalidated(bool $on = true): static
    {
        $this->invalidated = $on;
        return $this;
    }

    public function setFrozen(bool $on = true): static
    {
        $this->frozen = $on;
        return $this;
    }

    public function isFrozen(): bool
    {
        return $this->frozen;
    }

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
     * Visit every event registration in this subtree, calling
     * $visit($registration, int[] $path). Node types without events or
     * children contribute nothing; TagDomNode/ListDomNode override.
     *
     * @param callable(\Spwa\Events\EventRegistration, int[]): void $visit
     */
    public function walkRegistrations(callable $visit): void
    {
    }

    /** Bind every listener in this subtree (materialisation: initial render, insert, replace). */
    public function bindEvents(): void
    {
        $this->walkRegistrations(fn($reg, $path) => $reg->add($path));
    }

    /** Unbind every listener in this subtree (the node is leaving the tree). */
    public function unbindEvents(): void
    {
        $this->walkRegistrations(fn($reg, $path) => $reg->remove($path));
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
