<?php

namespace Spwa\Template;


use Spwa\Dom\HtmlNode;
use Spwa\Dom\HtmlText;

class TextNode extends Node
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    function render(NodePath $path): HtmlNode
    {
        return new HtmlText($this, $path, $this->text);
    }
}