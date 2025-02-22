<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;

class Link extends HtmlNode
{
    public function __construct(
        ?string $href = null
    )
    {
        parent::__construct();
        $this->setAttrs([
            "rel" => "stylesheet",
            "href" => $href
        ]);
    }

    function closed(): bool
    {
        return true;
    }


    function tag(): string
    {
        return "link";
    }
}