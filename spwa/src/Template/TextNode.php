<?php

namespace Spwa\Template;

use Spwa\Html\HtmlNode;
use Spwa\Html\HtmlTextNode;

class TextNode extends Node
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    function execute(): HtmlNode
    {
        return new HtmlTextNode($this->text);
    }
}