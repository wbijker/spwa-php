<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\HtmlText;

class InlineScript extends HtmlNode
{
    public function __construct(
        string $js = null,
        bool $ignore = false
    )
    {
        $this->ignore = $ignore;
        $this->setAttrs([
            "type" => "text/javascript",
        ]);
        $this->children = [new HtmlText($js)];
    }

    function tag(): string
    {
        return "script";
    }
}