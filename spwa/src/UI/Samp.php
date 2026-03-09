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

    public function render(): DomNode
    {
        return $this->node('samp')->children($this->content);
    }
}
