<?php

namespace Spwa\Js;

class History
{
    static function pushState($state, $title, string $url): void
    {
        Js::run(Js::invoke(
            Js::obj('history', 'pushState'),
            is_string($state) ? Js::str($state) : $state,
            is_string($title) ? Js::str($title) : $title,
            Js::str($url),
        ));
    }

    static function replaceState($state, $unused, string $url): void
    {
        Js::run(Js::invoke(
            Js::obj('history', 'replaceState'),
            is_string($state) ? Js::str($state) : $state,
            is_string($unused) ? Js::str($unused) : $unused,
            Js::str($url),
        ));
    }

    static function go(int $delta = 0): void
    {
        Js::run(Js::invoke(Js::obj('history', 'go'), $delta));
    }

    static function back(): void
    {
        Js::run(Js::invoke(Js::obj('history', 'back')));
    }

    static function forward(): void
    {
        Js::run(Js::invoke(Js::obj('history', 'forward')));
    }
}
