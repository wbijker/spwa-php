<?php

namespace Spwa\UI;

/**
 * Mark (highlighted) element.
 */
class Mark extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function render(): DomNode
    {
        return $this->node('mark')->children($this->content);
    }
}
