<?php

namespace Spwa\Nodes;

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

    function renderHtml(): string
    {
        return '(' . $this->pathStr() . ') ' . $this->text;
    }

    function initialize(?Node $parent, PathInfo $path, StateManager $manager): void
    {
        $path->set($this, $parent, true);
    }

    function finalize(StateManager $manager): void
    {
    }
}