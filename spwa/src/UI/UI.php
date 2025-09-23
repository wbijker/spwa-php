<?php

namespace Spwa\UI;


class UI
{
    static function rows(): Element
    {
        return new Element();
    }

    static function cols(): Element
    {
        return new Element();
    }

    static function columns(): Element
    {
        return new Element();
    }

    static function image(string $src, ?string $alt = null): Element
    {
        return new Element();
    }

    static function text(string $content): TextElement
    {
        return new TextElement();
    }
}


