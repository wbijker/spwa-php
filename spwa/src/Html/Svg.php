<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;

class Svg extends HtmlNode
{
    public function __construct(
        ?string $xmlns = null,
        ?string $fill = null,
        ?string $viewBox = null,
        ?float  $strokeWidth = null,
        ?string $stroke = null,
        ?string $class = null,
        ?array  $children = null
    )
    {
        $this->setAttrs([
            "xmlns" => $xmlns,
            "fill" => $fill,
            "viewBox" => $viewBox,
            "stroke-width" => $strokeWidth,
            "stroke" => $stroke,
            "class" => $class
        ]);
        $this->children = $children ?? [];
    }

    function tag(): string
    {
        return "svg";
    }
}
