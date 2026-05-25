<?php

namespace Spwa\Js;

class Console
{
    static function log(...$args): void
    {
        Js::invoke(["console", "log"], $args);
    }

    static function warn(...$args): void
    {
        Js::invoke(["console", "warn"], $args);
    }

    static function dir(...$args): void
    {
        Js::invoke(["console", "dir"], $args);
    }

    static function error(...$args): void
    {
        Js::invoke(["console", "error"], $args);
    }

    static function info(...$args): void
    {
        Js::invoke(["console", "info"], $args);
    }

    static function debug(...$args): void
    {
        Js::invoke(["console", "debug"], $args);
    }

    static function clear(): void
    {
        Js::invoke(["console", "clear"], []);
    }

    static function group(...$args): void
    {
        Js::invoke(["console", "group"], $args);
    }

    static function groupCollapsed(...$args): void
    {
        Js::invoke(["console", "groupCollapsed"], $args);
    }

    static function groupEnd(): void
    {
        Js::invoke(["console", "groupEnd"], []);
    }

    static function table(...$args): void
    {
        Js::invoke(["console", "table"], $args);
    }
}
