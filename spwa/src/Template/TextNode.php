<?php

namespace Spwa\Template;


class TextNode extends Node
{
    private string $text;

    public function __construct(string $text)
    {
        parent::__construct();
        $this->text = $text;
    }

    function render(): string
    {
        return $this->path->render() . htmlspecialchars($this->text, ENT_QUOTES, 'UTF-8');
    }


    function resolvePaths(NodePath $path): void
    {
        $this->path = $path;
    }
}