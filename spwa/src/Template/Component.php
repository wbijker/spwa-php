<?php

namespace Spwa\Template;

use Spwa\Html\HtmlNode;

abstract class Component extends Node
{
    abstract function render(): ElementNode;

    function execute(): HtmlNode
    {
        return $this->render()->execute();
    }
}