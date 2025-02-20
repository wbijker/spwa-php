<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlContentNode;

class Tbody extends HtmlContentNode
{
    function tag(): string
    {
        return "tbody";
    }
}