<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\PathInfo;
use Spwa\Nodes\StateManager;

class Head extends HtmlNode
{

    public function __construct(
        array $children
    )
    {
        parent::__construct($children);
    }

    function tag(): string
    {
        return 'head';
    }

}

