<?php

namespace Spwa\UI\Css;

/**
 * Known CSS properties with numeric indices for compression.
 */
enum CssProperty: int
{
    case Display = 0;
    case FlexDirection = 1;
    case JustifyContent = 2;
    case AlignItems = 3;
    case Gap = 4;
    case Padding = 5;
    case Margin = 6;
    case Width = 7;
    case Height = 8;
    case BackgroundColor = 9;
    case Color = 10;
    case FontSize = 11;
    case FontWeight = 12;
    case BorderRadius = 13;
    case BoxShadow = 14;
    case FlexWrap = 15;
    case FlexGrow = 16;
    case FlexShrink = 17;
    case Position = 18;
    case Top = 19;
    case Right = 20;
    case Bottom = 21;
    case Left = 22;
    case TextAlign = 23;
    case TextDecoration = 24;
    case LineHeight = 25;
    case Overflow = 26;
    case Opacity = 27;
    case ZIndex = 28;
    case Cursor = 29;
    case GridTemplateColumns = 30;
    case ColumnGap = 31;
    case RowGap = 32;
    case MaxWidth = 33;
    case MinWidth = 34;
    case MaxHeight = 35;
    case MinHeight = 36;
    case PaddingTop = 37;
    case PaddingRight = 38;
    case PaddingBottom = 39;
    case PaddingLeft = 40;
    case MarginTop = 41;
    case MarginRight = 42;
    case MarginBottom = 43;
    case MarginLeft = 44;
    case ObjectFit = 45;
    case ObjectPosition = 46;
    case BorderWidth = 47;
    case BorderStyle = 48;
    case BorderColor = 49;
    case Visibility = 50;
    case TransitionProperty = 51;
    case TransitionDuration = 52;
    case TransitionTimingFunction = 53;
    case Transform = 54;
    case FlexBasis = 55;
    case AlignSelf = 56;
    case JustifySelf = 57;
    case OverflowX = 58;
    case OverflowY = 59;

    /**
     * Get CSS property name.
     */
    public function toCss(): string
    {
        return match ($this) {
            self::Display => 'display',
            self::FlexDirection => 'flex-direction',
            self::JustifyContent => 'justify-content',
            self::AlignItems => 'align-items',
            self::Gap => 'gap',
            self::Padding => 'padding',
            self::Margin => 'margin',
            self::Width => 'width',
            self::Height => 'height',
            self::BackgroundColor => 'background-color',
            self::Color => 'color',
            self::FontSize => 'font-size',
            self::FontWeight => 'font-weight',
            self::BorderRadius => 'border-radius',
            self::BoxShadow => 'box-shadow',
            self::FlexWrap => 'flex-wrap',
            self::FlexGrow => 'flex-grow',
            self::FlexShrink => 'flex-shrink',
            self::Position => 'position',
            self::Top => 'top',
            self::Right => 'right',
            self::Bottom => 'bottom',
            self::Left => 'left',
            self::TextAlign => 'text-align',
            self::TextDecoration => 'text-decoration',
            self::LineHeight => 'line-height',
            self::Overflow => 'overflow',
            self::Opacity => 'opacity',
            self::ZIndex => 'z-index',
            self::Cursor => 'cursor',
            self::GridTemplateColumns => 'grid-template-columns',
            self::ColumnGap => 'column-gap',
            self::RowGap => 'row-gap',
            self::MaxWidth => 'max-width',
            self::MinWidth => 'min-width',
            self::MaxHeight => 'max-height',
            self::MinHeight => 'min-height',
            self::PaddingTop => 'padding-top',
            self::PaddingRight => 'padding-right',
            self::PaddingBottom => 'padding-bottom',
            self::PaddingLeft => 'padding-left',
            self::MarginTop => 'margin-top',
            self::MarginRight => 'margin-right',
            self::MarginBottom => 'margin-bottom',
            self::MarginLeft => 'margin-left',
            self::ObjectFit => 'object-fit',
            self::ObjectPosition => 'object-position',
            self::BorderWidth => 'border-width',
            self::BorderStyle => 'border-style',
            self::BorderColor => 'border-color',
            self::Visibility => 'visibility',
            self::TransitionProperty => 'transition-property',
            self::TransitionDuration => 'transition-duration',
            self::TransitionTimingFunction => 'transition-timing-function',
            self::Transform => 'transform',
            self::FlexBasis => 'flex-basis',
            self::AlignSelf => 'align-self',
            self::JustifySelf => 'justify-self',
            self::OverflowX => 'overflow-x',
            self::OverflowY => 'overflow-y',
        };
    }

    /**
     * Try to find property by CSS name.
     */
    public static function fromCss(string $name): ?self
    {
        static $map = null;
        if ($map === null) {
            $map = [];
            foreach (self::cases() as $case) {
                $map[$case->toCss()] = $case;
            }
        }
        return $map[$name] ?? null;
    }

    /**
     * Get all properties as array for JS (index = position).
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
