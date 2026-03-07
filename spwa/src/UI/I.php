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

    public function render(): Node
    {
        return $this->node('i')->children($this->content);
    }
}
