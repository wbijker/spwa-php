<?php

namespace Spwa\UI;

/**
 * Bold element (stylistic, use Strong for importance).
 */
class B extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function render(): DomNode
    {
        return $this->node('b')->children($this->content);
    }
}
