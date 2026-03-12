<?php

namespace Spwa\UI\Css;

/**
 * CSS pseudo-classes/elements with numeric indices for compression.
 */
enum CssPseudo: int
{
    case Hover = 0;
    case Active = 1;
    case Focus = 2;
    case FocusVisible = 3;
    case FocusWithin = 4;
    case Visited = 5;
    case Disabled = 6;
    case Enabled = 7;
    case Checked = 8;
    case Required = 9;
    case Valid = 10;
    case Invalid = 11;
    case Placeholder = 12;
    case First = 13;
    case Last = 14;
    case Only = 15;
    case Odd = 16;
    case Even = 17;
    case Empty = 18;

    /**
     * Get CSS selector.
     */
    public function toSelector(): string
    {
        return match ($this) {
            self::Hover => ':hover',
            self::Active => ':active',
            self::Focus => ':focus',
            self::FocusVisible => ':focus-visible',
            self::FocusWithin => ':focus-within',
            self::Visited => ':visited',
            self::Disabled => ':disabled',
            self::Enabled => ':enabled',
            self::Checked => ':checked',
            self::Required => ':required',
            self::Valid => ':valid',
            self::Invalid => ':invalid',
            self::Placeholder => '::placeholder',
            self::First => ':first-child',
            self::Last => ':last-child',
            self::Only => ':only-child',
            self::Odd => ':nth-child(odd)',
            self::Even => ':nth-child(even)',
            self::Empty => ':empty',
        };
    }

    /**
     * Get the class prefix.
     */
    public function toPrefix(): string
    {
        return match ($this) {
            self::Hover => 'hover',
            self::Active => 'active',
            self::Focus => 'focus',
            self::FocusVisible => 'focus-visible',
            self::FocusWithin => 'focus-within',
            self::Visited => 'visited',
            self::Disabled => 'disabled',
            self::Enabled => 'enabled',
            self::Checked => 'checked',
            self::Required => 'required',
            self::Valid => 'valid',
            self::Invalid => 'invalid',
            self::Placeholder => 'placeholder',
            self::First => 'first',
            self::Last => 'last',
            self::Only => 'only',
            self::Odd => 'odd',
            self::Even => 'even',
            self::Empty => 'empty',
        };
    }

    /**
     * Get all pseudos as array for JS (index = position).
     */
    public static function toArray(): array
    {
        $arr = [];
        foreach (self::cases() as $case) {
            $arr[$case->value] = $case->toSelector();
        }
        return $arr;
    }
}
