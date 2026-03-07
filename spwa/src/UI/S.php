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

    public function render(): Node
    {
        return $this->node('s')->children($this->content);
    }
}
