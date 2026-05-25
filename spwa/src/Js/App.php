<?php

namespace Spwa\Js;

class App
{
    static function refresh(): void
    {
        Js::invoke(["spwa", "refresh"], []);
    }
}