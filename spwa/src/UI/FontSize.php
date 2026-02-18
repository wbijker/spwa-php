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
}
