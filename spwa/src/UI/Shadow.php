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

    public function getCssValue(): string
    {
        return match ($this) {
            self::None => 'none',
            self::Small => '0 1px 2px 0 rgb(0 0 0 / 0.05)',
            self::Medium => '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
            self::Large => '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
            self::ExtraLarge => '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)',
            self::TwoXL => '0 25px 50px -12px rgb(0 0 0 / 0.25)',
        };
    }
}
