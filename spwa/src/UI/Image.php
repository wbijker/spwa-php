<?php

namespace Spwa\UI;

/**
 * Image element.
 *
 * Object-fit/position helpers live on UIElement (objectCover, objectContain,
 * objectFill, objectNone, objectCenter/Top/Bottom/Left/Right).
 *
 * Usage:
 *   UI::image("/photo.jpg", "Profile photo")
 *       ->size(Unit::tick(48))
 *       ->rounded()
 *       ->objectCover()
 */
class Image extends UIElement
{
    public function __construct(string $src, string $alt = '')
    {
        parent::__construct('img');
        $this->attr('src', $src);
        $this->attr('alt', $alt);
    }

    /**
     * Make responsive (max-width: 100%, height: auto).
     */
    public function responsive(): static
    {
        $this->addStyle('max-w-full', ['max-width' => '100%']);
        $this->addStyle('h-auto', ['height' => 'auto']);
        return $this;
    }
}
