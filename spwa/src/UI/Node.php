<?php

namespace Spwa\UI;

/**
 * Base class for DOM nodes.
 */
abstract class Node
{
    protected string|int|null $key = null;

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
