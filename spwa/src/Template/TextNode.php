<?php

namespace Spwa\Template;

use Spwa\Html\HtmlTagNode;
use Spwa\Html\HtmlTextNode;

class TextNode extends Node
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    function execute(HtmlTagNode $node): void
    {
        $node->addChild(new HtmlTextNode($this->text));
    }
}