<?php

namespace Spwa\Html;

class A extends HtmlContentNodeParent
{
    public function __construct(
        mixed        $key = null,
        ?string      $class = null,
        ?string      $id = null,
        mixed        $style = null,
        ?array       $data = null,
        ?MouseEvents $mouse = null,
        string       $href = "#",
        ?array       $children = null)
    {
        parent::__construct($key, $class, $id, $style, $data, $mouse, $children);
        $this->setAttrs(["href" => $href]);
    }

    function tag(): string
    {
        return "a";
    }
}