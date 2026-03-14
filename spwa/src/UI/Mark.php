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

    public function build(): DomNode
    {
        return $this->dom()->setTag('mark')->children($this->content);
    }
}
