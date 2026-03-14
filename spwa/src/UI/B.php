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

    public function build(): DomNode
    {
        return $this->dom()->setTag('b')->children($this->content);
    }
}
