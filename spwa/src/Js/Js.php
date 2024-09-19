<?php

namespace Spwa\Js;

class JS
{
    static function log(...$args)
    {
        JsRuntime::invoke(['console', 'log'], $args);
    }

    static function alert(string $message)
    {
        JsRuntime::invoke(['alert'], [$message]);
    }
}
