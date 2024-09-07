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
     * @var NodeAttributeEvent[] $events
     */
    private array $events = [];

    /**
     * @param string $tag
     * @param (Node|NodeAttribute)[] $items
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
            $element->addAttribute($attribute);
        }
        foreach ($this->children as $index => $child) {
            $element->addChild($child->render($path->addClone($index), $state));
        }
        foreach ($this->events as $event) {
            $state->set($path)->addEvent($event->name, $event->handler);

            $function = new JsFunction("handleEvent", $event->name, $path->path, "event");
            $element->addAttribute(new NodeAttributeText("on" . $event->name, $function->dump()));
        }
        return $element;
    }

}

