<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlText;

abstract class Component extends Node
{
    abstract function view(): ElementNode;

    private ?ElementNode $result = null;

    private function getView(): ElementNode
    {
        return $this->result ??= $this->view();
    }

    function render(NodePath $path, EventListeners $listeners): \Spwa\Dom\HtmlNode
    {
        $template = $this->getView();
        return $template->render($path, $listeners);
    }
}