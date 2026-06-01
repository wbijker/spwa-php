<?php

namespace BrickPHP\Js;

class Console
{
    static function log(...$args): void           { self::call('log', $args); }
    static function warn(...$args): void          { self::call('warn', $args); }
    static function dir(...$args): void           { self::call('dir', $args); }
    static function error(...$args): void         { self::call('error', $args); }
    static function info(...$args): void          { self::call('info', $args); }
    static function debug(...$args): void         { self::call('debug', $args); }
    static function group(...$args): void         { self::call('group', $args); }
    static function groupCollapsed(...$args): void{ self::call('groupCollapsed', $args); }
    static function table(...$args): void         { self::call('table', $args); }
    static function clear(): void                 { self::call('clear', []); }
    static function groupEnd(): void              { self::call('groupEnd', []); }

    private static function call(string $method, array $args): void
    {
        // PHP strings → JS string literals; everything else flows through
        // Js::invoke which json_encodes it as a JS literal.
        $args = array_map(fn($a) => is_string($a) ? Js::str($a) : $a, $args);
        Js::run(Js::invoke(Js::obj('console', $method), ...$args));
    }
}
