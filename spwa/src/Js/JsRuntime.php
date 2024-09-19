<?php

namespace Spwa\Js;

class JsRuntime
{
    static array $calls = [];

    static function invoke(array $path, array $args)
    {
        self::$calls[] = [$path, $args];
    }

    static function dump(): string
    {
        $run = json_encode(self::$calls);
        self::$calls = [];
        return $run;
    }
}