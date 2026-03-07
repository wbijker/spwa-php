<?php

namespace Spwa\UI;

/**
 * Underline element.
 */
class U extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function render(): Node
    {
        return $this->node('u')->children($this->content);
    }
}
