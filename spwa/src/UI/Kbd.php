<?php

namespace Spwa\UI;

/**
 * Keyboard input element.
 */
class Kbd extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function build(): DomNode
    {
        return $this->dom()->setTag('kbd')->children($this->content);
    }
}
