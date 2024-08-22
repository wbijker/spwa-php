<?php

namespace Spwa\Template;


class TextNode extends Node
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    function render(): string
    {
        return htmlspecialchars($this->text, ENT_QUOTES, 'UTF-8');
    }
}