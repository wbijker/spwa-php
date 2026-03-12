<?php

namespace Spwa\UI\Css;

/**
 * Known CSS values with numeric indices for compression.
 */
enum CssValue: int
{
    case Flex = 0;
    case Block = 1;
    case Inline = 2;
    case InlineBlock = 3;
    case Grid = 4;
    case None = 5;
    case Row = 6;
    case Column = 7;
    case RowReverse = 8;
    case ColumnReverse = 9;
    case FlexStart = 10;
    case FlexEnd = 11;
    case Center = 12;
    case SpaceBetween = 13;
    case SpaceAround = 14;
    case SpaceEvenly = 15;
    case Stretch = 16;
    case Baseline = 17;
    case Wrap = 18;
    case Nowrap = 19;
    case Absolute = 20;
    case Relative = 21;
    case Fixed = 22;
    case Sticky = 23;
    case Auto = 24;
    case Zero = 25;
    case ZeroPx = 26;
    case Full = 27;
    case One = 28;
    case Pointer = 29;
    case Inherit = 30;
    case Transparent = 31;
    case White = 32;
    case Black = 33;
    case Cover = 34;
    case Contain = 35;
    case Fill = 36;
    case Underline = 37;
    case Bold = 38;
    case Normal = 39;
    case Hidden = 40;
    case Visible = 41;
    case Scroll = 42;
    case Solid = 43;
    case Dashed = 44;
    case Dotted = 45;
    case All = 46;
    case InlineFlex = 47;
    case FitContent = 48;
    case MinContent = 49;
    case MaxContent = 50;

    /**
     * Get CSS value string.
     */
    public function toCss(): string
    {
        return match ($this) {
            self::Flex => 'flex',
            self::Block => 'block',
            self::Inline => 'inline',
            self::InlineBlock => 'inline-block',
            self::Grid => 'grid',
            self::None => 'none',
            self::Row => 'row',
            self::Column => 'column',
            self::RowReverse => 'row-reverse',
            self::ColumnReverse => 'column-reverse',
            self::FlexStart => 'flex-start',
            self::FlexEnd => 'flex-end',
            self::Center => 'center',
            self::SpaceBetween => 'space-between',
            self::SpaceAround => 'space-around',
            self::SpaceEvenly => 'space-evenly',
            self::Stretch => 'stretch',
            self::Baseline => 'baseline',
            self::Wrap => 'wrap',
            self::Nowrap => 'nowrap',
            self::Absolute => 'absolute',
            self::Relative => 'relative',
            self::Fixed => 'fixed',
            self::Sticky => 'sticky',
            self::Auto => 'auto',
            self::Zero => '0',
            self::ZeroPx => '0px',
            self::Full => '100%',
            self::One => '1',
            self::Pointer => 'pointer',
            self::Inherit => 'inherit',
            self::Transparent => 'transparent',
            self::White => '#ffffff',
            self::Black => '#000000',
            self::Cover => 'cover',
            self::Contain => 'contain',
            self::Fill => 'fill',
            self::Underline => 'underline',
            self::Bold => 'bold',
            self::Normal => 'normal',
            self::Hidden => 'hidden',
            self::Visible => 'visible',
            self::Scroll => 'scroll',
            self::Solid => 'solid',
            self::Dashed => 'dashed',
            self::Dotted => 'dotted',
            self::All => 'all',
            self::InlineFlex => 'inline-flex',
            self::FitContent => 'fit-content',
            self::MinContent => 'min-content',
            self::MaxContent => 'max-content',
        };
    }

    /**
     * Try to find value by CSS string.
     */
    public static function fromCss(string $value): ?self
    {
        static $map = null;
        if ($map === null) {
            $map = [];
            foreach (self::cases() as $case) {
                $map[$case->toCss()] = $case;
            }
        }
        return $map[$value] ?? null;
    }

    /**
     * Get all values as array for JS (index = position).
     */
    public static function toArray(): array
    {
        $arr = [];
        foreach (self::cases() as $case) {
            $arr[$case->value] = $case->toCss();
        }
        return $arr;
    }
}
