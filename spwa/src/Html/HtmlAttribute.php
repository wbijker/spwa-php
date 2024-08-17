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
        $escapedName = htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8');
        $escapedValue = htmlspecialchars($this->value, ENT_QUOTES, 'UTF-8');
        return "$escapedName=\"$escapedValue\"";
    }
}