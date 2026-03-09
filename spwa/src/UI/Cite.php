<?php

namespace Spwa\UI;

/**
 * Citation element.
 */
class Cite extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function render(): DomNode
    {
        return $this->node('cite')->children($this->content);
    }
}
