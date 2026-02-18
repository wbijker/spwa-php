<?php

namespace Spwa\UI;

/**
 * Font weights.
 */
enum FontWeight: string
{
    case Thin = 'thin';
    case ExtraLight = 'extralight';
    case Light = 'light';
    case Normal = 'normal';
    case Medium = 'medium';
    case SemiBold = 'semibold';
    case Bold = 'bold';
    case ExtraBold = 'extrabold';
    case Black = 'black';

    public function toClass(): string
    {
        return 'font-' . $this->value;
    }
}
