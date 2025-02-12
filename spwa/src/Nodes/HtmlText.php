<?php

namespace Spwa\Nodes;

use Spwa\Dom\DomNode;
use Spwa\Dom\TextDomNode;
use Spwa\Js\JS;

class HtmlText extends Node
{
    public function __construct(private string $text)
    {
    }

    function compare(Node $node, PatchBuilder $patch): void
    {
        if (!($node instanceof HtmlText)) {
            // nodes are not the same replace whole node
            $patch->replace($this, $node);
            return;
        }
        if ($this->text != $node->text) {
            $patch->text($this, $node->text);
        }
    }

    function renderHtml(RenderContext $context): DomNode
    {
        return new TextDomNode($this, $context->current, $this->text);
    }
    
    function finalize(StateManager $manager): void
    {
    }
}