<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlNode;
use Spwa\Dom\HtmlElement;

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
    private array $children = [];

    /**
     * @var NodeAttribute[] $attributes
     */
    private array $attributes = [];

    /**
     * @param string $tag
     * @param Node|NodeAttribute[] $items
     */
    public function __construct(string $tag, array $items)
    {
        $this->tag = $tag;
        $this->items = $items;

        foreach ($items as $item) {
            if ($item instanceof NodeAttribute) {
                // text, event
                $this->attributes[] = $item;
            } else {
                $this->children[] = $item;
            }
        }
    }


    function render(NodePath $path, EventListeners $listeners): HtmlNode
    {
        $element = new HtmlElement($this, $path, $this->tag);
        foreach ($this->attributes as $attribute) {
            $element->addAttribute($attribute);
        }
        foreach ($this->children as $index => $child) {
            $element->addChild($child->render($path->addClone($index), $listeners));
        }
        return $element;
    }
}

