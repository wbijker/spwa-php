<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlContentNode;

class Td extends HtmlContentNode
{
    function tag(): string
    {
        return "td";
    }
}