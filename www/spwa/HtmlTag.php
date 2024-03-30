<?php

namespace Spwa\Web;

// Represents an HTML tag.
// It implements the HtmlTagFacade interface, to avoid the need for a separate tags and attributes in constructing HTML
class HtmlTag extends Node implements HtmlBuildable
{
    public string $tag;
    public array $attrs;
    /**
     * @var Node[] $children
     */
    public array $children;

    /**
     * @param string $tag
     * @param array $attrs
     * @param HtmlTag[] $children
     */
    public function __construct(string $tag, array $attrs = [], array $children = [])
    {
        $this->tag = $tag;
        $this->attrs = $attrs;
        $this->children = $children;
    }

    public function execute(HtmlTag $tag): void
    {
        // add this to $tags children
        $tag->children[] = $this;
    }

    function render(): void
    {
        echo "<$this->tag";
        foreach ($this->attrs as $key => $value) {
            echo " $key=\"$value\"";
        }
        echo ">";
        foreach ($this->children as $child) {
            $child->render();
        }
        echo "</$this->tag>";
    }
}