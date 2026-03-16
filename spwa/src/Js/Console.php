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
        JsRuntime::invoke(["console", "dir"], $args);
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

    static function clear(): void
    {
        JsRuntime::invoke(["console", "clear"], []);
    }

    static function group(...$args): void
    {
        JsRuntime::invoke(["console", "group"], $args);
    }

    static function groupCollapsed(...$args): void
    {
        JsRuntime::invoke(["console", "groupCollapsed"], $args);
    }

    static function groupEnd(): void
    {
        JsRuntime::invoke(["console", "groupEnd"], []);
    }

    static function table(...$args): void
    {
        JsRuntime::invoke(["console", "table"], $args);
    }
}
