<?php

namespace Spwa\Js;

class History
{
    static function pushState($state, $title, string $url): void
    {
        Js::invoke(["history", "pushState"], [$state, $title, $url]);
    }

    static function replaceState($state, $unused, string $url): void
    {
        Js::invoke(["history", "replaceState"], [$state, $unused, $url]);
    }

    static function go(int $delta = 0): void
    {
        Js::invoke(["history", "go"], [$delta]);
    }

    static function back(): void
    {
        Js::invoke(["history", "back"], []);
    }

    static function forward(): void
    {
        Js::invoke(["history", "forward"], []);
    }
}