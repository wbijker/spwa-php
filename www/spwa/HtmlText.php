<?php

namespace Spwa\Web;

class HtmlText extends Node implements HtmlBuildable
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    function render(): void
    {
        echo htmlspecialchars($this->value);
    }

    public function execute(HtmlTag $tag): void
    {
        $tag->children[] = $this;
    }
}