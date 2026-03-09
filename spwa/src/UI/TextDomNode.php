<?php

namespace Spwa\UI;

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
}
