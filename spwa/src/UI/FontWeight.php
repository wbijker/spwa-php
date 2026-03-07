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

    public function getCssValue(): string
    {
        return match ($this) {
            self::Thin => '100',
            self::ExtraLight => '200',
            self::Light => '300',
            self::Normal => '400',
            self::Medium => '500',
            self::SemiBold => '600',
            self::Bold => '700',
            self::ExtraBold => '800',
            self::Black => '900',
        };
    }
}
