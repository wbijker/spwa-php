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

    public function build(): DomNode
    {
        return $this->dom()->setTag('cite')->children($this->content);
    }
}
