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

    public function render(): DomNode
    {
        return $this->node('i')->children($this->content);
    }
}
