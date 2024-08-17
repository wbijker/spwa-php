<?php

namespace Spwa\Template;

use Spwa\Html\HtmlTagNode;

class AttributeNode extends Node
{
    private string $name;
    private string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    function execute(HtmlTagNode $node): void
    {
        $node->addAttribute($this->name, $this->value);
    }
}

class ElementNode extends Node
{
    public string $tag;
    /**
     * @var Node[] $items
     */
    private array $items;

    /**
     * @param string $tag
     * @param Node[] $items
     */
    public function __construct(string $tag, array $items)
    {
        $this->tag = $tag;
        $this->items = $items;
    }

    function execute(HtmlTagNode $node): void
    {
        $root = new HtmlTagNode($this->tag);
        foreach ($this->items as $item) {
            $item->execute($root);
        }
        $node->addChild($root);
    }
}

