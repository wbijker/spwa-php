<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlContentNode;

class Tr extends HtmlContentNode
{
    function tag(): string
    {
        return "tr";
    }
}