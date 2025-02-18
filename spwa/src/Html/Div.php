<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlContentNode;


class Div extends HtmlContentNode
{

    public function __construct(
        mixed        $key = null,
        ?string      $class = null,
        ?string      $id = null,
        ?array       $style = null,
        ?array       $data = null,
        ?MouseEvents $mouse = null,
        ?array       $children = null)
    {
        parent::__construct($key, $class, $id, $style, $data, $mouse);
        $this->children = $children ?? [];
    }

    function tag(): string
    {
        return "div";
    }
}
