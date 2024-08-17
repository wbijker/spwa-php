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