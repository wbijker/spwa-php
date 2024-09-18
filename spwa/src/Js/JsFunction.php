<?php

namespace Spwa\Js;

class JsVar {
    public string $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

}

class JsFunction
{
    private string $name;
    private array $args;

    /**
     * @param string $name
     * @param array $args
     */
    public function __construct(string $name, ...$args)
    {
        $this->name = $name;
        $this->args = $args;
    }

    function dump(): string
    {
        $args = array_map(fn($arg) => $arg instanceof JsVar ? $arg->name : json_encode($arg), $this->args);
        return "$this->name(" . implode(", ", $args) . ")";
    }
}