<?php

namespace Spwa\UI;

/**
 * Emphasis element.
 */
class Em extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function render(): Node
    {
        return $this->node('em')->children($this->content);
    }
}
