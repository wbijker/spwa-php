<?php

namespace Spwa\UI;

/**
 * Font sizes.
 */
enum FontSize: string
{
    case ExtraSmall = 'xs';
    case Small = 'sm';
    case Base = 'base';
    case Large = 'lg';
    case ExtraLarge = 'xl';
    case TwoXL = '2xl';
    case ThreeXL = '3xl';
    case FourXL = '4xl';
    case FiveXL = '5xl';
    case SixXL = '6xl';

    public function toClass(): string
    {
        return 'text-' . $this->value;
    }

    public function getCssValue(): string
    {
        return match ($this) {
            self::ExtraSmall => '0.75rem',
            self::Small => '0.875rem',
            self::Base => '1rem',
            self::Large => '1.125rem',
            self::ExtraLarge => '1.25rem',
            self::TwoXL => '1.5rem',
            self::ThreeXL => '1.875rem',
            self::FourXL => '2.25rem',
            self::FiveXL => '3rem',
            self::SixXL => '3.75rem',
        };
    }
}
