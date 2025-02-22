<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;


class Meta extends HtmlNode
{
    function __construct(
        public ?string $name = null,
        public ?string $httpEquiv = null,
        public ?string $charset = null,
        public ?string $content = null,
        public ?string $property = null
    )
    {
        parent::__construct();
        $this->setAttrs([
            "name" => $name,
            "http-equiv" => $httpEquiv,
            "charset" => $charset,
            "content" => $content,
            "property" => $property
        ]);
    }

    function tag(): string
    {
        return "meta";
    }

    function closed(): bool
    {
        return true;
    }

}