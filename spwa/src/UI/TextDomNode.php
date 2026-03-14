<?php

namespace Spwa\UI;

use Spwa\VNode\Patcher;

/**
 * A DOM node that wraps text content in a <span> element.
 * Always renders as an element so it has a stable path for diffing.
 */
class TextDomNode extends DomNode
{
    public function __construct(
        protected string $content
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function collectStyles(): array
    {
        return [];
    }

    public function toHtml(): string
    {
        $pathAttr = $this->managed ? ' data-path="' . implode(',', $this->path) . '"' : '';
        return "<span{$pathAttr}>" . htmlspecialchars($this->content) . "</span>";
    }

    public function compare(DomNode $other, Patcher $patcher): void
    {
        if (!$other instanceof TextDomNode) {
            $patcher->replaceNode($this->path, $this);
        } elseif ($this->content !== $other->content) {
            $patcher->replaceText($this->path, $this->content);
        }
    }
}
