<?php

namespace Spwa\UI;

/**
 * Subscript element.
 */
class Sub extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function build(): DomNode
    {
        return $this->dom()->setTag('sub')->children($this->content);
    }
}
