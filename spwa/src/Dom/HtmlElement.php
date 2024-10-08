<?php

namespace Spwa\Dom;

use Spwa\Patch;
use Spwa\Template\Node;
use Spwa\Template\NodeAttribute;
use Spwa\Template\NodeAttributeText;
use Spwa\Template\NodePath;
use function Spwa\Template\padSpace;


class HtmlElement extends HtmlNode
{
    public string $tag;
    /**
     * @var HtmlNode[] $children
     */
    private array $children = [];

    private array $attributes;

    /**
     * @param Node $owner
     * @param NodePath $path
     * @param string $tag
     */
    public function __construct(Node $owner, NodePath $path, string $tag)
    {
        parent::__construct($owner, $path);
        $this->tag = $tag;
        $this->children = [];
        $this->attributes = [];
    }

    public static function compare(HtmlElement $prev, HtmlElement $next, array &$patches)
    {
        if ($prev->tag !== $next->tag || count($prev->children) !== count($next->children)) {
            $patches[] = Patch::replace($prev->path, $next);
            return;
        }

        foreach ($prev->children as $i => $child) {
            HtmlNode::compareNodes($child, $next->children[$i], $patches);
        }

        // compare attributes
    }

    public function addChild(HtmlNode $child): void
    {
        $this->children[] = $child;
    }

    public function addAttribute(NodeAttribute $attribute): void
    {
        $this->attributes[] = $attribute;
    }

    function render(): string
    {
        $this->attributes[] = new NodeAttributeText("path", $this->path->render());

        $attributes = padSpace(implode(" ", array_map(fn(NodeAttribute $attr) => $attr->render(), $this->attributes)));
        $children = implode("", array_map(fn(HtmlNode $child) => $child->render(), $this->children));

        return "<$this->tag$attributes>$children</$this->tag>";
    }
}