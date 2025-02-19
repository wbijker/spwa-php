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

    public static function create(string $name, ...$args): string
    {
        return (new JsFunction($name, ...$args))->dump();
    }

    function dump(): string
    {
        $args = array_map(fn($arg) => $arg instanceof JsLiteral ? $arg->name : htmlspecialchars(json_encode($arg), ENT_QUOTES), $this->args);
        return "$this->name(" . implode(", ", $args) . ")";
    }
}