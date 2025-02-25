<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlContentNode;

abstract class HtmlContentNodeParent extends HtmlContentNode
{
    public function __construct(
        mixed        $key = null,
        ?string      $class = null,
        ?string      $id = null,
        mixed        $style = null,
        ?array       $data = null,
        ?MouseEvents $mouse = null,
        ?array       $children = null)
    {
        parent::__construct($key, $class, $id, $style, $data, $mouse);
        $this->children = $children ?? [];
    }
}