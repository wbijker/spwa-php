<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Nodes\PatchBuilder;
use Spwa\Nodes\PathInfo;
use Spwa\Nodes\StateManager;

class InlineScript extends HtmlNode
{
    function initializeAndCompare(?Node $parent, PathInfo $current, StateManager $manager, Node $old, PatchBuilder $patch): void
    {
    }

    public function __construct(
        string $js = null
    )
    {
        $this->setAttrs([
            "type" => "text/javascript",
        ]);
        $this->children = [new HtmlText($js)];
    }

    function tag(): string
    {
        return "script";
    }
}