<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlContentNode;

class Table extends HtmlContentNode
{
    function tag(): string
    {
        return "table";
    }
}

