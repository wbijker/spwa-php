<?php

namespace Spwa\Dom;

use Spwa\Template\Node;
use Spwa\Template\NodePath;

class HtmlFragment extends HtmlNode
{
    /**
     * @var HtmlNode[] $children
     */
    private array $children;

    /**
     * @param Node $owner
     * @param NodePath $path
     * @param HtmlNode[] $children
     */
    public function __construct(Node $owner, NodePath $path, array $children = [])
    {
        parent::__construct($owner, $path);
        $this->children = $children;
    }

    function render(): string
    {
        return implode("", array_map(fn(HtmlNode $child) => $child->render(), $this->children));
    }
}