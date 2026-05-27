<?php

namespace Spwa\Js;

/**
 * Server-side queue of JS statements to ship to the browser. Callers
 * construct each statement as a raw string and append it via run().
 * The framework drains the queue at response time, emits the strings
 * inline in <head> on the initial GET, and ships them as an array in
 * the POST response so the client can `new Function(stmt)()` each.
 *
 *   Js::run(Js::invoke(Js::obj('console', 'log'), Js::str('hello')));
 *   Js::run(Js::assign(Js::obj('document', 'title'), Js::str('SPWA')));
 *
 * Helpers like Console / Document / History / Location wrap common
 * call shapes.
 */
class Js
{
    /** @var string[] Pending JS statements */
    static array $calls = [];

    /** Queue a raw JS statement. Runs on the client in queue order. */
    static function run(string $js): void
    {
        self::$calls[] = $js;
    }

    /**
     * Queue a `SPWA.ready(function(){ … })` wrapper around one or more
     * statements. The inner block runs after DOMContentLoaded (or
     * immediately if already past it), so it's the right home for any
     * setup that needs the body to exist — relevant on the initial GET
     * where the head-script runs before <body> parses.
     *
     *   Js::ready(
     *       Js::assign($ref, Js::invoke(Js::obj('L', 'map'), Js::str($key))),
     *       Js::invoke(Js::obj($tile, 'addTo'), $ref),
     *   );
     */
    static function ready(string ...$lines): void
    {
        self::$calls[] = 'SPWA.ready(function(){' . implode(';', $lines) . '})';
    }

    /** A JS string literal — `Js::str("hi")` → `"hi"`. */
    static function str(string $s): string
    {
        return json_encode($s, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Dotted property path: `Js::obj("a", "b", "c")` → `a.b.c`. Segments
     * are inserted verbatim, so you can pass another helper's output as
     * the base — e.g. `Js::obj(Js::invoke(Js::obj("L", "map"), Js::str("m")), "setView")`
     * → `L.map("m").setView`.
     */
    static function obj(string ...$path): string
    {
        return implode('.', $path);
    }

    /**
     * Bracket-form property access: `Js::index("story-map")` → `["story-map"]`,
     * `Js::index(5)` → `[5]`. The key is always rendered as a JS literal
     * (strings get quoted, ints stay as numbers) — distinct from
     * `Js::invoke`'s rule of "strings verbatim" because here the key is
     * always a value, never a raw expression.
     *
     *   Js::obj('window', 'leafLet') . Js::index($key)  // → window.leafLet["…"]
     */
    static function index(mixed $key): string
    {
        return '[' . json_encode($key, JSON_UNESCAPED_SLASHES) . ']';
    }

    /**
     * Assignment expression: `Js::assign($ref, Js::invoke(...))` → `$ref=…`.
     * Follows the same string-vs-value rule as `invoke`: a string `$right`
     * is inserted verbatim (so chained helper output composes), anything
     * else is JSON-encoded into a JS literal.
     *
     *   Js::assign(Js::obj('document', 'title'), Js::str('SPWA'))
     *   // → document.title="SPWA"
     */
    static function assign(string $left, mixed $right): string
    {
        $rendered = is_string($right) ? $right : json_encode($right, JSON_UNESCAPED_SLASHES);
        return $left . '=' . $rendered;
    }

    /**
     * Build a JS call expression: `Js::invoke(Js::obj("console","log"), Js::str("hi"), 42)`
     * → `console.log("hi",42)`. Variadic args are mixed:
     *
     *   - **string** args are inserted verbatim — chain helper outputs
     *     and raw JS expressions (`Js::str("hi")`, `Js::obj(…)`,
     *     `Js::invoke(…)`) flow through unchanged.
     *   - **non-string** args (ints, floats, arrays, bools, null) are
     *     JSON-encoded so they appear as JS literals.
     *
     * `$name` is verbatim too — chain through `Js::obj(...)` for paths.
     */
    static function invoke(string $name, mixed ...$args): string
    {
        $rendered = array_map(
            fn($a) => is_string($a) ? $a : json_encode($a, JSON_UNESCAPED_SLASHES),
            $args,
        );
        return $name . '(' . implode(',', $rendered) . ')';
    }

    /** Re-insert statements at the front of the queue (reordering). */
    static function prepend(array $calls): void
    {
        array_unshift(self::$calls, ...$calls);
    }

    /** @return string[] Take and clear the pending queue. */
    static function drain(): array
    {
        $calls = self::$calls;
        self::$calls = [];
        return $calls;
    }

    /** @return string[] Wire format for the client response. */
    static function dump(): array
    {
        return self::$calls;
    }
}
