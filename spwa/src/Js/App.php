<?php

namespace Spwa\Js;

class App
{
    static function refresh(): void
    {
        JsRuntime::invoke(["spwa", "refresh"], []);
    }
}