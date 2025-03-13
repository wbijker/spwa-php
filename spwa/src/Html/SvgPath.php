<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;

class SvgPath extends HtmlNode
{
    public function __construct(
        ?string $strokeLinecap = null,
        ?string $strokeLinejoin = null,
        ?string $d = null
    )
    {
        $this->setAttrs([
            "stroke-linecap" => $strokeLinecap,
            "stroke-linejoin" => $strokeLinejoin,
            "d" => $d
        ]);
    }

    function tag(): string
    {
        return 'path';
    }
}