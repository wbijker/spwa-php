<?php

namespace Spwa\UI;

use Spwa\VNode\Patcher;

/**
 * Renders as an HTML comment. Used as a placeholder for null children
 * to preserve stable indices in the DOM tree for diffing.
 */
class CommentDomNode extends DomNode
{
    public function collectStyles(): array
    {
        return [];
    }

    public function toHtml(): string
    {
        return '<!---->';
    }

    public function compare(DomNode $other, Patcher $patcher): void
    {
        if (!$other instanceof CommentDomNode) {
            $patcher->replaceNode($this->path, $this);
        }
    }
}
