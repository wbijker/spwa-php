<?php

namespace Spwa\Dom;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\PathInfo;

abstract class DomNode
{

    /**
     * @param Node $owner
     * @param PathInfo $path
     */
    public function __construct(
        public Node      $owner,
        public PathInfo  $path)
    {
    }

    abstract function renderHtml(): string;
}

