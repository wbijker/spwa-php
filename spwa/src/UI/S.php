<?php

namespace Spwa\UI;

/**
 * Strikethrough element.
 */
class S extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function build(): DomNode
    {
        return $this->dom()->setTag('s')->children($this->content);
    }
}
