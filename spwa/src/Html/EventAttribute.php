<?php

namespace Spwa\Html;

class EventAttribute extends BaseAttribute
{
    private string $name;
    private string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function __toString(): string
    {
        return "$this->name=\"$this->value\"";
    }

    function render(): string
    {
        return "";
    }
}