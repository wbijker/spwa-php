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

    function resolvePaths(NodePath $path): void
    {
        parent::resolvePaths($path);
        $this->getView()->resolvePaths($path);
    }

    function render(): string
    {
        return $this->getView()->render();
    }
}