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

    public function build(): DomNode
    {
        return $this->dom()->setTag('strong')->children($this->content);
    }
}
