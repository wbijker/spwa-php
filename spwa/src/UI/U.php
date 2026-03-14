<?php

namespace Spwa\UI;

/**
 * Underline element.
 */
class U extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function build(): DomNode
    {
        return $this->dom()->setTag('u')->children($this->content);
    }
}
