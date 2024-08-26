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

    function render(NodePath $path, EventListeners $listeners): HtmlNode
    {
        $element = new HtmlElement($this, $path, $this->tag);
        foreach ($this->attributes as $attribute) {
            $element->addAttribute($attribute);
        }
        foreach ($this->children as $index => $child) {
            $element->addChild($child->render($path->addClone($index), $listeners));
        }
        foreach ($this->events as $event) {
            $listeners->addEvent(new Event($event->name, $path, $event->handler));
        }
        return $element;
    }

    function compare(ElementNode $other): void
    {
        if ($this->tag != $other->tag) {
            // replace the whole node
            return;
        }
        if (count($this->children) != count($other->children)) {
            // replace the whole node
            return;
        }

        // compare attributes

        // compare children
        $count = count($this->children);
        for ($i = 0; $i < $count; $i++) {
            $oldChild = $this->children[$i];
            $newChild = $other->children[$i];
            Node::compareNode($oldChild, $newChild);
        }
    }
}

