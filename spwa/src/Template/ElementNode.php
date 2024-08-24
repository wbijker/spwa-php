<?php

namespace Spwa\Template;

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

        foreach ($items as $item) {
            if ($item instanceof NodeAttribute) {
                $this->attributes[] = $item;
            } else {
                $this->children[] = $item;
            }
        }
    }

    public function render(): string
    {
        $this->attributes[] = new NodeAttribute("path", $this->path->render());

        $attributes = padSpace(implode(" ", array_map(fn(NodeAttribute $attr) => $attr->render(), $this->attributes)));
        $children = implode("", array_map(fn(Node $child) => $child->render(), $this->children));

        return "<$this->tag$attributes>$children</$this->tag>";
    }

    function resolvePaths(NodePath $path): void
    {
        parent::resolvePaths($path);
        foreach ($this->children as $index => $child) {
            $child->resolvePaths($this->path->addClone($index));
        }
    }
}

