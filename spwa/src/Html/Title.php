<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\HtmlText;

class Title extends HtmlNode
{
    function __construct(public string $title)
    {
        $this->children = [new HtmlText($title)];
    }

    function tag(): string
    {
        return "title";
    }
    
}