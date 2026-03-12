<?php

namespace Spwa\UI\Css;

/**
 * CSS breakpoints with numeric indices for compression.
 */
enum CssBreakpoint: int
{
    case Sm = 0;
    case Md = 1;
    case Lg = 2;
    case Xl = 3;
    case Xxl = 4;

    /**
     * Get the media query condition.
     */
    public function toMediaQuery(): string
    {
        return match ($this) {
            self::Sm => '(min-width: 640px)',
            self::Md => '(min-width: 768px)',
            self::Lg => '(min-width: 1024px)',
            self::Xl => '(min-width: 1280px)',
            self::Xxl => '(min-width: 1536px)',
        };
    }

    /**
     * Get the class prefix.
     */
    public function toPrefix(): string
    {
        return match ($this) {
            self::Sm => 'sm',
            self::Md => 'md',
            self::Lg => 'lg',
            self::Xl => 'xl',
            self::Xxl => '2xl',
        };
    }

    /**
     * Get all breakpoints as array for JS (index = position).
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
