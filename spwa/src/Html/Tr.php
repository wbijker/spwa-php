<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlContentNode;

class Tr extends HtmlContentNodeParent
{
    function tag(): string
    {
        return "tr";
    }
}