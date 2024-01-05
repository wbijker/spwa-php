<?php

class JsRuntime
{
    static array $pendingCalls = [];

    static function invoke(array $path, array $args)
    {
        self::$pendingCalls[] = [$path, $args];
    }
}

class JS
{
    static function log(...$args)
    {
        JsRuntime::invoke(['console', 'log'], $args);
    }

    static function alert(string $message)
    {
        JsRuntime::invoke(['alert'], [$message]);
    }
}

