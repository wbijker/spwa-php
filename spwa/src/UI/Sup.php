<?php

namespace Spwa\UI;

/**
 * Superscript element.
 */
class Sup extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function build(): DomNode
    {
        return $this->dom()->setTag('sup')->children($this->content);
    }
}
