<?php

namespace Spwa\Html;

function padSpace(string $implode): string
{
    return $implode ? " $implode" : "";
}

class HtmlTagNode extends HtmlNode
{
    private string $tag;
    /**
     * @var BaseAttribute[]
     */
    private array $attributes;

    /**
     * @var HtmlTagNode[] $children
     */
    private array $children;

    /**
     * @param string $tag
     * @param BaseAttribute[] $attributes
     * @param HtmlTagNode[] $children
     */
    public function __construct(string $tag, array $attributes = [], array $children = [])
    {
        $this->tag = $tag;
        $this->attributes = $attributes;
        $this->children = $children;
    }

    public function addChild(HtmlNode $child): void
    {
        $this->children[] = $child;
    }

    public function render(): string
    {
        $attributes = padSpace(implode(" ", array_map(fn(BaseAttribute $attr) => $attr->render(), $this->attributes)));
        $children = implode("", array_map(fn(HtmlNode $child) => $child->render(), $this->children));

        return "<$this->tag$attributes>$children</$this->tag>";
    }

    public function addAttribute(string $name, string $value): void
    {
        $this->attributes[] = new HtmlAttribute($name, $value);
    }

}