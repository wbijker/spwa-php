<?php

namespace Spwa\UI;

/**
 * Small text element.
 */
class Small extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function build(): DomNode
    {
        return $this->dom()->setTag('small')->children($this->content);
    }
}
