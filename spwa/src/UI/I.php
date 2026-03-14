<?php

namespace Spwa\UI;

/**
 * Italic element (stylistic, use Em for emphasis).
 */
class I extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function build(): DomNode
    {
        return $this->dom()->setTag('i')->children($this->content);
    }
}
