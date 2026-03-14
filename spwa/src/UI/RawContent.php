<?php

namespace Spwa\UI;

use Spwa\VNode\Patcher;

/**
 * Represents raw HTML content that should not be wrapped in a span.
 * Used for injecting raw strings into TagDomNode (e.g., style/script content).
 */
class RawContent extends DomNode
{
    public function __construct(
        private string $content
    ) {
    }

    public function collectStyles(): array
    {
        return [];
    }

    public function toHtml(): string
    {
        return $this->content;
    }

    public function compare(DomNode $other, Patcher $patcher): void
    {
    }
}
