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

    public function render(): Node
    {
        return $this->node('sub')->children($this->content);
    }
}
