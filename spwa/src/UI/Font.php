<?php

namespace Spwa\UI;

/**
 * Font size values.
 */
enum FontSizeEnum: string
{
    case XS = 'xs';
    case SM = 'sm';
    case Base = 'base';
    case LG = 'lg';
    case XL = 'xl';
    case XL2 = '2xl';
    case XL3 = '3xl';
    case XL4 = '4xl';
    case XL5 = '5xl';
    case XL6 = '6xl';
    case XL7 = '7xl';
    case XL8 = '8xl';
    case XL9 = '9xl';
}

/**
 * Font weight values.
 */
enum FontWeightEnum: string
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
}

/**
 * Font family values.
 */
enum FontFamilyEnum: string
{
    case Sans = 'sans';
    case Serif = 'serif';
    case Mono = 'mono';
}

/**
 * Text alignment values.
 */
enum TextAlignEnum: string
{
    case Left = 'left';
    case Center = 'center';
    case Right = 'right';
    case Justify = 'justify';
    case Start = 'start';
    case End = 'end';
}

/**
 * Stateful font size value.
 */
class FontSize extends StateValue
{
    public function __construct(
        protected FontSizeEnum $size
    ) {
    }

    protected function getBaseClass(): string
    {
        return 'text-' . $this->size->value;
    }

    public static function xs(): static
    {
        return new static(FontSizeEnum::XS);
    }

    public static function sm(): static
    {
        return new static(FontSizeEnum::SM);
    }

    public static function base(): static
    {
        return new static(FontSizeEnum::Base);
    }

    public static function lg(): static
    {
        return new static(FontSizeEnum::LG);
    }

    public static function xl(): static
    {
        return new static(FontSizeEnum::XL);
    }

    public static function xl2(): static
    {
        return new static(FontSizeEnum::XL2);
    }

    public static function xl3(): static
    {
        return new static(FontSizeEnum::XL3);
    }

    public static function xl4(): static
    {
        return new static(FontSizeEnum::XL4);
    }

    public static function xl5(): static
    {
        return new static(FontSizeEnum::XL5);
    }

    public static function xl6(): static
    {
        return new static(FontSizeEnum::XL6);
    }

    public static function xl7(): static
    {
        return new static(FontSizeEnum::XL7);
    }

    public static function xl8(): static
    {
        return new static(FontSizeEnum::XL8);
    }

    public static function xl9(): static
    {
        return new static(FontSizeEnum::XL9);
    }
}

/**
 * Stateful font weight value.
 */
class FontWeight extends StateValue
{
    public function __construct(
        protected FontWeightEnum $weight
    ) {
    }

    protected function getBaseClass(): string
    {
        return 'font-' . $this->weight->value;
    }

    public static function thin(): static
    {
        return new static(FontWeightEnum::Thin);
    }

    public static function extraLight(): static
    {
        return new static(FontWeightEnum::ExtraLight);
    }

    public static function light(): static
    {
        return new static(FontWeightEnum::Light);
    }

    public static function normal(): static
    {
        return new static(FontWeightEnum::Normal);
    }

    public static function medium(): static
    {
        return new static(FontWeightEnum::Medium);
    }

    public static function semiBold(): static
    {
        return new static(FontWeightEnum::SemiBold);
    }

    public static function bold(): static
    {
        return new static(FontWeightEnum::Bold);
    }

    public static function extraBold(): static
    {
        return new static(FontWeightEnum::ExtraBold);
    }

    public static function black(): static
    {
        return new static(FontWeightEnum::Black);
    }
}

/**
 * Stateful font family value.
 */
class FontFamily extends StateValue
{
    public function __construct(
        protected FontFamilyEnum $family
    ) {
    }

    protected function getBaseClass(): string
    {
        return 'font-' . $this->family->value;
    }

    public static function sans(): static
    {
        return new static(FontFamilyEnum::Sans);
    }

    public static function serif(): static
    {
        return new static(FontFamilyEnum::Serif);
    }

    public static function mono(): static
    {
        return new static(FontFamilyEnum::Mono);
    }
}

/**
 * Stateful text alignment value.
 */
class TextAlign extends StateValue
{
    public function __construct(
        protected TextAlignEnum $align
    ) {
    }

    protected function getBaseClass(): string
    {
        return 'text-' . $this->align->value;
    }

    public static function left(): static
    {
        return new static(TextAlignEnum::Left);
    }

    public static function center(): static
    {
        return new static(TextAlignEnum::Center);
    }

    public static function right(): static
    {
        return new static(TextAlignEnum::Right);
    }

    public static function justify(): static
    {
        return new static(TextAlignEnum::Justify);
    }

    public static function start(): static
    {
        return new static(TextAlignEnum::Start);
    }

    public static function end(): static
    {
        return new static(TextAlignEnum::End);
    }
}
