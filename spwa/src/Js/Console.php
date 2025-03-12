<?php

namespace Spwa\Js;

class Console
{
    static function log(...$args): void
    {
        JsRuntime::invoke(["console", "log"], $args);
    }

    static function warn(...$args): void
    {
        JsRuntime::invoke(["console", "warn"], $args);
    }

    static function dir(...$args): void
    {
        JsRuntime::invoke(["console", "warn"], $args);
    }

    static function error(...$args): void
    {
        JsRuntime::invoke(["console", "error"], $args);

    }

    static function info(...$args): void
    {
        JsRuntime::invoke(["console", "info"], $args);

    }

    static function debug(...$args): void
    {
        JsRuntime::invoke(["console", "debug"], $args);

    }
}