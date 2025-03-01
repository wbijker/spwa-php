<?php

namespace Spwa\Nodes;

use Spwa\Js\Console;

class PatchBuilder
{

    var $patches = [];

    function addPatch(Node $node, int $type, mixed $content) {
        $this->patches[] = [$node->path->indexPath, $type, $content];
    }

    function replace(Node $old, Node $new): void
    {
        Console::log("Replace whole node", $new->renderHtml());
    }

    function text(Node $node, string $text): void
    {
        $this->addPatch($node, 0, $text);
    }

    function updateAttr(Node $node, string $attr, ?string $value): void
    {
    }
}


