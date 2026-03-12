<?php

namespace Spwa\UI\Css;

/**
 * CSS color scheme preferences with numeric indices for compression.
 */
enum CssColorScheme: int
{
    case Dark = 0;
    case Light = 1;

    /**
     * Get the media query condition.
     */
    public function toMediaQuery(): string
    {
        return match ($this) {
            self::Dark => '(prefers-color-scheme: dark)',
            self::Light => '(prefers-color-scheme: light)',
        };
    }

    /**
     * Get the class prefix.
     */
    public function toPrefix(): string
    {
        return match ($this) {
            self::Dark => 'dark',
            self::Light => 'light',
        };
    }

    /**
     * Get all schemes as array for JS (index = position).
     */
    public static function toArray(): array
    {
        $arr = [];
        foreach (self::cases() as $case) {
            $arr[$case->value] = $case->toMediaQuery();
        }
        return $arr;
    }
}
