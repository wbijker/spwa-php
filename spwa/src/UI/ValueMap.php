<?php

namespace Spwa\UI;

/**
 * Inline lookup helper for values that depend on a runtime key.
 *
 * Usage:
 *
 *   ->color(ValueMap::create([
 *       'banking'  => Color::blue(600),
 *       'mobile'   => Color::violet(600),
 *       'internet' => Color::cyan(600),
 *   ], Color::gray(600), $this->category))
 *
 * At runtime it's the obvious `$options[$key] ?? $default`. At build time
 * the CssExtractor recognises ValueMap::create calls and harvests every
 * value in $options + $default as candidate classes — so the dynamic key
 * doesn't hide which styles can actually appear.
 *
 * The $options array can be an inline literal (parsed from source) or a
 * property reference (resolved via Reflection at extraction time), but it
 * MUST be declared inline at the call site or as a class property — the
 * extractor doesn't follow method calls or local variables.
 */
class ValueMap
{
    /**
     * @template T
     * @param array<string|int, T> $options
     * @param T $default
     * @param string|int|null $key
     * @return T
     */
    public static function create(array $options, mixed $default, mixed $key): mixed
    {
        if ($key === null) return $default;
        return $options[$key] ?? $default;
    }
}
