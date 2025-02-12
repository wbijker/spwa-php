<?php

namespace Spwa\Dom;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\PathInfo;

abstract class DomNode
{

    /**
     * @param Node $owner
     * @param HtmlNode|null $parent
     * @param PathInfo $path
     */
    public function __construct(
        public Node      $owner,
        public ?HtmlNode $parent,
        public PathInfo  $path)
    {
    }

    abstract function renderHtml(): string;
}

