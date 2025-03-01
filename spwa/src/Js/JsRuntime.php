<?php

namespace Spwa\Js;

class JsRuntime
{
    static array $calls = [];

    static function invoke(array $path, array $args): void
    {
        self::$calls[] = ['invoke', $path, $args];
    }

    static function assign(array $obj,  $value): void
    {
        self::$calls[] = ['assign', $obj, $value];
    }

    static function dump(): array
    {
        $run = self::$calls;
        self::$calls = [];
        return $run;
    }
}