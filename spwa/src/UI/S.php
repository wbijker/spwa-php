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

    public function render(): DomNode
    {
        return $this->node('s')->children($this->content);
    }
}
