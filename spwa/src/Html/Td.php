<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlContentNode;

class Td extends HtmlContentNodeParent
{
    function tag(): string
    {
        return "td";
    }
}