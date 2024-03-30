<?php

namespace Spwa\Web;

class HtmlAttr implements HtmlBuildable
{
    public string $name;
    public string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function execute(HtmlTag $tag): void
    {
        $tag->attrs[$this->name] = $this->value;
    }
}


