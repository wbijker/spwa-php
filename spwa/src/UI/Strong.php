<?php

namespace Spwa\UI;

/**
 * Strong importance element.
 */
class Strong extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function render(): DomNode
    {
        return $this->node('strong')->children($this->content);
    }
}
