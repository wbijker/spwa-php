<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;

class Img extends HtmlNode
{
    public function __construct(
        ?string $src = null,
        ?string $alt = null,
        ?array  $style = null
    )
    {
        $this->setAttrs([
            "src" => $src,
            "alt" => $alt,
        ]);
        $this->setStyle($style);
    }

    function tag(): string
    {
        return "img";
    }
}