<?php

namespace Spwa\Template;

abstract class Component extends Node
{
    abstract function view(): ElementNode;

    private ?ElementNode $result = null;

    private function getView(): ElementNode
    {
        return $this->result ??= $this->view();
    }

    function resolvePaths(NodePath $parent): void
    {
//        $this->getView()->resolvePaths($index, $path);
    }

    function render(): string
    {
        return $this->getView()->render();
    }
}