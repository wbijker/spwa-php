<?php

namespace Spwa\Nodes;

class PatchBuilder
{

    var $patches = [];

    function addPatch(Node $node, string $type, mixed $content): void
    {
        $this->patches[] = [$node->path?->indexPath ?? [], $type, $content];
    }

    function replace(Node $new, Node $old): void
    {
        $this->addPatch($old, 'replace', $new->renderHtml());
    }

    function text(Node $node, string $text): void
    {
        $this->addPatch($node, 'text', $text);
    }

    function updateAttr(Node $node, string $attr, ?string $value): void
    {
    }
}


