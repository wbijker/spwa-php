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

    static function prepend(array $calls): void
    {
        array_unshift(self::$calls, ...$calls);
    }

    static function drain(): array
    {
        $calls = self::$calls;
        self::$calls = [];
        return $calls;
    }

    static function dump(): array
    {
        return self::$calls;
    }
}