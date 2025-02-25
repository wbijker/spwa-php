<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlContentNode;

class Tbody extends HtmlContentNodeParent
{
    function tag(): string
    {
        return "tbody";
    }
}