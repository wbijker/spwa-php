<?php

namespace Spwa\UI;

/**
 * Keyboard input element.
 */
class Kbd extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function render(): Node
    {
        return $this->node('kbd')->children($this->content);
    }
}
