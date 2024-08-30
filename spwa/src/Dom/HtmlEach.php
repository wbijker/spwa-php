<?php

namespace Spwa\Dom;

use Spwa\Template\Node;
use Spwa\Template\NodePath;

class HtmlEach extends HtmlNode
{
    /**
     * @var KeyedNode[] $children
     */
    private array $children;

    /**
     * @param Node $owner
     * @param NodePath $path
     * @param KeyedNode[] $children
     */
    public function __construct(Node $owner, NodePath $path, array $children = [])
    {
        parent::__construct($owner, $path);
        $this->children = $children;
    }

    public static function compare(HtmlEach $prev, HtmlEach $next, array &$patches)
    {

    }

    function render(): string
    {
        return implode("", array_map(fn(KeyedNode $child) => $child->node->render(), $this->children));
    }
}