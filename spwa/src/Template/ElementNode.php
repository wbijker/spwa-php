<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlNode;
use Spwa\Dom\HtmlElement;
use Spwa\Js\JsFunction;
use Spwa\Js\JsRaw;

function padSpace(string $implode): string
{
    return $implode ? " $implode" : "";
}

class ElementNode extends Node
{
    public string $tag;
    /**
     * @var Node[] $children
     */
    public array $children = [];

    /**
     * @var NodeAttribute[] $attributes
     */
    public array $attributes = [];

    /**
     * @param string $tag
     * @param (Node|NodeAttribute|string)[] $items
     */
    public function __construct(string $tag, array $items)
    {
        $this->tag = $tag;
        $this->attributes = [];

        foreach ($items as $item) {
            if (is_string($item)) {
                $this->children[] = new TextNode($item);
            } else if ($item instanceof NodeAttribute) {
                $this->attributes[] = $item;
            } else {
                $this->children[] = $item;
            }
        }
    }

    function addChild(Node $child): void
    {
        $this->children[] = $child;
    }

    function render(NodePath $path, PathState $state): HtmlNode
    {
        // reset when body hit
        if ($this->tag == "body") {
            $path = new NodePath([]);
        }
        $element = new HtmlElement($this, $path, $this->tag);
        foreach ($this->attributes as $attribute) {
            $attribute->bind($element, $path, $state);
        }
        foreach ($this->children as $index => $child) {
            $element->addChild($child->render($path->addClone($index), $state));
        }
        return $element;
    }

}

