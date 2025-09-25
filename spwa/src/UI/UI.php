<?php

namespace Spwa\UI;


class UI
{
    static function element(): Element
    {
        return new Element();
    }

    static function rows(): Element
    {
        return new FlexElement();
    }

    static function cols(): Element
    {
        return new FlexElement();
    }

    static function image(string $src, ?string $alt = null): Element
    {
        return new Element();
    }

    static function text(string $text): TextElement
    {
        return new TextElement($text);
    }
}


