<?php

namespace Spwa\UI;

use Spwa\VNode\Patcher;

/**
 * Represents a text node in the DOM.
 */
class TextDomNode extends DomNode
{
    public function __construct(
        protected string $content
    ) {
    }

    /**
     * Get the text content.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Collect all styles (text nodes have none).
     * @return array<string, array<string, string>>
     */
    public function collectStyles(): array
    {
        return [];
    }

    /**
     * Render to HTML string.
     */
    public function toHtml(): string
    {
        return htmlspecialchars($this->content);
    }

    /**
     * Compare this text node with another and generate patches.
     */
    public function compare(DomNode $other, Patcher $patcher): void
    {
        if (!$other instanceof TextDomNode) {
            // Different node type, replace entirely
            $patcher->replaceNode($this->path, $this);
        } elseif ($this->content !== $other->content) {
            // Same type but different text, just replace text
            $patcher->replaceText($this->path, $this->content);
        }
    }
}
