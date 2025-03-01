<?php

namespace Spwa\Nodes;

use Spwa\Js\JS;

class HtmlText extends Node
{
    public function __construct(private string $text)
    {
    }

    function initializeAndCompare(?Node $parent, PathInfo $current, StateManager $manager, Node $old, PatchBuilder $patch): void
    {
        $this->path = $current;

        if (!($old instanceof HtmlText)) {
            // nodes are not the same replace whole node
            $patch->replace($this, $old);
            return;
        }

        if ($this->text != $old->text) {
            $patch->text($this, $this->text);
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