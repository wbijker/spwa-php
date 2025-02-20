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

    function find(array $path): ?Node
    {
        if (count($path) == 0) {
            return $this;
        }
        return null;
    }

    function renderHtml(): string
    {
//        return '(' . $this->path->pathStr() . ') ' . $this->text;
        return htmlentities($this->text);
    }

    function initialize(?Node $parent, PathInfo $current, StateManager $manager): void
    {
        // text is always a leaf node
        $this->path = $current;
    }

    function finalize(StateManager $manager): void
    {
    }
}