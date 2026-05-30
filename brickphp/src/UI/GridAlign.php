<?php

namespace BrickPHP\UI;

/**
 * Alignment keyword for CSS grid `justify-*` / `align-*` / `place-*`
 * properties. Grid uses `start` / `end` (not flexbox's `flex-start` /
 * `flex-end`), so this is intentionally separate from Align (which is
 * flexbox-oriented). The backing value is the class-name token; css()
 * returns the actual CSS keyword.
 *
 * The distribution values (SpaceBetween/Around/Evenly) only apply to the
 * *content* properties (justify-content / align-content / place-content);
 * the *items* and *self* properties take Start/End/Center/Stretch.
 */
enum GridAlign: string
{
    case Start = 'start';
    case End = 'end';
    case Center = 'center';
    case Stretch = 'stretch';
    case SpaceBetween = 'between';
    case SpaceAround = 'around';
    case SpaceEvenly = 'evenly';

    /** CSS keyword. */
    public function css(): string
    {
        return match ($this) {
            self::SpaceBetween => 'space-between',
            self::SpaceAround  => 'space-around',
            self::SpaceEvenly  => 'space-evenly',
            default            => $this->value,
        };
    }
}
