<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlContentNode;

class Table extends HtmlContentNodeParent
{
    function tag(): string
    {
        return "table";
    }
}

