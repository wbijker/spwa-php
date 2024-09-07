<?php

namespace Spwa\Js;

abstract class JsVar
{
    abstract function dump(): string;

    static function infer($var): JsVar
    {
        if ($var instanceof JsVar) {
            return $var;
        }
        if (is_null($var) || is_int($var) || is_float($var) || is_bool($var)) {
            return new JsRaw($var);
        }
        if (is_string($var)) {
            return new JsString($var);
        }
        if (is_array($var)) {
            return new JsArray(array_map(fn($item) => JsVar::infer($item), $var));
        }
        return new JsRaw(null);
    }
}

