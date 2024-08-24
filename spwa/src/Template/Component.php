<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlText;

abstract class Component extends Node
{
    abstract function view(): ElementNode;

    function render(NodePath $path, EventListeners $listeners): \Spwa\Dom\HtmlNode
    {
        $template = $this->view();
        return $template->render($path, $listeners);
    }
}