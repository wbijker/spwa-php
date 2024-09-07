<?php

namespace Spwa\Template;


use Spwa\Dom\HtmlNode;
use Spwa\Dom\HtmlText;

class TextNode extends Node
{
    private string $text;
    private bool $escape;

    public function __construct(string $text, bool $escape = true)
    {
        $this->text = $text;
        $this->escape = $escape;
    }

    function render(NodePath $path, PathState $state): HtmlNode
    {
        return new HtmlText($this, $path, $this->text, $this->escape);
    }

}