<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\PathInfo;
use Spwa\Nodes\RenderContext;
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

    function renderHtml(RenderContext $context): string
    {
        return parent::renderHtml($context->next($this, PathInfo::root()));
    }


//    function initialize(?Node $parent, PathInfo $path, StateManager $manager): void
//    {
//        parent::initialize($parent, PathInfo::root(), $manager);
//    }
}