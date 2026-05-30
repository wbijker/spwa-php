<?php

namespace BrickPHP\Js;

class Location
{
    static function assign(string $url): void
    {
        Js::run(Js::invoke(Js::obj('location', 'assign'), Js::str($url)));
    }

    static function reload(bool $forceGet = false): void
    {
        Js::run(Js::invoke(Js::obj('location', 'reload'), $forceGet));
    }

    static function replace(string $url): void
    {
        Js::run(Js::invoke(Js::obj('location', 'replace'), Js::str($url)));
    }
}
