<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;

class Script extends HtmlNode
{
    public function __construct(
        ?string $src = null
    )
    {
        $this->setAttrs([
            "type" => "text/javascript",
            "src" => $src
        ]);
    }

    function tag(): string
    {
        return "script";
    }
}

