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

    public function build(): DomNode
    {
        return $this->dom()->setTag('em')->children($this->content);
    }
}
