<?php

namespace Spwa\Html;

class HtmlAttribute extends BaseAttribute
{
    private string $name;
    private string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    function render(): string
    {
        return "$this->name=\"$this->value\"";
    }
}