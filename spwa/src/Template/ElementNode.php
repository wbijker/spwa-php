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
    public array $children = [];

    /**
     * @var NodeAttribute[] $attributes
     */
    public array $attributes = [];

    /**
     * @var NodeAttributeEvent[] $events
     */
    private array $events = [];

    /**
     * @param string $tag
     * @param Node|NodeAttribute[] $items
     */
    public function __construct(string $tag, array $items)
    {
        $this->tag = $tag;
        $this->items = $items;

        foreach ($items as $item) {
            if ($item instanceof NodeAttributeText) {
                $this->attributes[] = $item;
            } else if ($item instanceof NodeAttributeEvent) {
                $this->events[] = $item;
            } else {
                $this->children[] = $item;
            }
        }
    }

    function render(NodePath $path, PathState $state): HtmlNode
    {
        $element = new HtmlElement($this, $path, $this->tag);
        foreach ($this->attributes as $attribute) {
            $element->addAttribute($attribute);
        }
        foreach ($this->children as $index => $child) {
            $element->addChild($child->render($path->addClone($index), $state));
        }
        foreach ($this->events as $event) {
            $state->set($path)->addEvent($event->name, $event->handler);
        }
        return $element;
    }

}

