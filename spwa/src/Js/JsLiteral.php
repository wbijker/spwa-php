<?php

namespace Spwa\Js;

class JsLiteral
{
    public string $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

}