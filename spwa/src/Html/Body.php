<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\PathInfo;
use Spwa\Nodes\StateManager;

class Body extends HtmlNode
{

    public function __construct(
        array $children
    )
    {
        $this->children = $children;
    }

    function tag(): string
    {
        return 'body';
    }

    function initialize(?Node $parent, PathInfo $path, StateManager $manager): void
    {
        $p = new PathInfo(0, null);
        foreach ($this->children as $child) {
            $child->initialize($this, $p, $manager);
        }
    }
}