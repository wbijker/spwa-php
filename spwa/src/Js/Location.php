<?php

namespace Spwa\Js;


class Location
{
    static function assign(string $url): void
    {
        JsRuntime::invoke(["location", "assign"], [$url]);
    }

    static function reload(bool $forceGet = false): void
    {
        JsRuntime::invoke(["location", "reload"], [$forceGet]);
    }

    static function replace(string $url): void
    {
        JsRuntime::invoke(["location", "replace"], [$url]);
    }
}