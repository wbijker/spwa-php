<?php

namespace Spwa\Template;

use Spwa\Html\HtmlNode;
use Spwa\Html\HtmlTagNode;

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

    function execute(): HtmlNode
    {
        $root = new HtmlTagNode($this->tag);
        foreach ($this->children as $item) {
            $root->addChild($item->execute());
        }
        foreach ($this->attributes as $attribute) {
            $root->addAttribute($attribute->name, $attribute->value);
        }
        return $root;
    }
}

