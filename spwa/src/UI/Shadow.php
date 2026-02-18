<?php

namespace Spwa\UI;

/**
 * Shadow sizes.
 */
enum Shadow: string
{
    case None = 'none';
    case Small = 'sm';
    case Medium = 'md';
    case Large = 'lg';
    case ExtraLarge = 'xl';
    case TwoXL = '2xl';

    public function toClass(): string
    {
        return match ($this) {
            self::None => 'shadow-none',
            self::Small => 'shadow-sm',
            self::Medium => 'shadow-md',
            self::Large => 'shadow-lg',
            self::ExtraLarge => 'shadow-xl',
            self::TwoXL => 'shadow-2xl',
        };
    }
}
