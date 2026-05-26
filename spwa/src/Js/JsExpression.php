<?php

namespace Spwa\Js;

/**
 * A queued JS call expression. Returned by Js::invoke / Js::assign so
 * one expression can be nested inside another's `path` (or `args`),
 * producing call-chain syntax on the client. Self-contained: toJs()
 * recursively serializes any embedded JsExpression children.
 *
 *   $m  = Js::invoke(['L', 'map'], ['map']);            // L.map("map")
 *   Js::invoke([$m, 'setView'], [[51.505, -0.09], 13]); // L.map("map").setView([51.505,-0.09],13)
 *
 * Each embedded child is pulled out of the pending queue when its
 * parent is constructed, so the inner expression only fires once — as
 * part of the outer statement.
 */
class JsExpression implements JsStatement
{
    public function __construct(
        public readonly string $mode, // 'invoke' or 'assign'
        public readonly array $path,
        public readonly mixed $args,  // array for invoke, single value for assign
    ) {}

    public function toJs(): string
    {
        // 'raw' bypasses path/args entirely — used by helpers like
        // Js::domReady() that need to emit a wrapper statement around
        // already-rendered inner expressions.
        if ($this->mode === 'raw') {
            return (string) $this->args;
        }

        $pathStr = self::renderPath($this->path);

        if ($this->mode === 'invoke') {
            $argsJs = implode(',', array_map(self::renderValue(...), $this->args));
            return $pathStr . '(' . $argsJs . ')';
        }

        // assign
        return $pathStr . '=' . self::renderValue($this->args);
    }

    private static function renderPath(array $path): string
    {
        $out = '';
        foreach ($path as $i => $segment) {
            if ($segment instanceof JsExpression) {
                $piece = $segment->toJs();
            } elseif ($segment instanceof JsLiteral) {
                $piece = $segment->name;
            } else {
                $piece = (string) $segment;
            }
            $out .= ($i === 0 ? '' : '.') . $piece;
        }
        return $out;
    }

    private static function renderValue(mixed $value): string
    {
        if ($value instanceof JsExpression) {
            return $value->toJs();
        }
        if ($value instanceof JsLiteral) {
            // Raw identifier or expression — emit verbatim instead of JSON-encoding.
            return $value->name;
        }
        return json_encode($value, JSON_UNESCAPED_SLASHES);
    }
}
