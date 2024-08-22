<?php

namespace Spwa\Template;

abstract class Component extends Node
{
    abstract function view(): ElementNode;

    function render(): string
    {
        return $this->view()->render();
    }
}