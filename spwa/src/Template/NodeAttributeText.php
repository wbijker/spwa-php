<?php

namespace Spwa\Template;


class NodeAttributeText extends NodeAttribute
{
    public string $name;
    public string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function render(): string
    {
        $escapedName = htmlspecialchars($this->name, ENT_COMPAT, 'UTF-8');
        $escapedValue = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
        return "$escapedName=\"$escapedValue\"";
    }
}