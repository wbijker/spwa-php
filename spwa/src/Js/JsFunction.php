<?php

namespace Spwa\Js;

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
        $args = array_map(fn($arg) => json_encode($arg), $this->args);
        return "$this->name(" . implode(", ", $args) . ")";
    }
}