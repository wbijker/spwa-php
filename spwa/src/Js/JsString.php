<?php

namespace Spwa\Js;

class JsString extends JsVar
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    function dump(): string
    {
        return "'$this->value'";
    }
}