<?php

namespace Spwa\UI;

/**
 * Superscript element.
 */
class Sup extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function render(): Node
    {
        return $this->node('sup')->children($this->content);
    }
}
