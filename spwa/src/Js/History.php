<?php

namespace Spwa\Js;

class History
{
    static function pushState($state, $title, string $url): void
    {
        JsRuntime::invoke(["history", "pushState"], [$state, $title, $url]);
    }

    static function replaceState($state, $unused, string $url): void
    {
        JsRuntime::invoke(["history", "replaceState"], [$state, $unused, $url]);
    }

    static function go(int $delta = 0): void
    {
        JsRuntime::invoke(["history", "go"], [$delta]);
    }

    static function back(): void
    {
        JsRuntime::invoke(["history", "back"], []);
    }

    static function forward(): void
    {
        JsRuntime::invoke(["history", "forward"], []);
    }
}