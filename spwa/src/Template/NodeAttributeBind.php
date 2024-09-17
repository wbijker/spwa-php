<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlElement;

class NodeAttributeBind extends NodeAttribute
{
    private string $value;

    public function __construct(string &$value)
    {
        $this->value = &$value;
    }

    function bind(HtmlElement $element, NodePath $path, PathState $state): void
    {

    }

}