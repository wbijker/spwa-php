<?php

namespace Spwa\UI;

/**
 * Sample output element.
 */
class Samp extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function build(): DomNode
    {
        return $this->dom()->setTag('samp')->children($this->content);
    }
}
