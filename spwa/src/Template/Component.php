<?php

namespace Spwa\Template;

use Spwa\Html\HtmlTagNode;

abstract class Component extends Node
{
    abstract function render(): ElementNode;

    function execute(HtmlTagNode $node): void
    {
        $this->render()->execute($node);
    }
}