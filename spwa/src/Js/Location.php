<?php

namespace Spwa\Js;


class Location
{
    static function assign(string $url): void
    {
        Js::invoke(["location", "assign"], [$url]);
    }

    static function reload(bool $forceGet = false): void
    {
        Js::invoke(["location", "reload"], [$forceGet]);
    }

    static function replace(string $url): void
    {
        Js::invoke(["location", "replace"], [$url]);
    }
}