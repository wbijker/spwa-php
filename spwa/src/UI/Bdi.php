<?php

namespace Spwa\UI;

/**
 * Bidirectional isolation element.
 */
class Bdi extends UIElement
{
    public function __construct(protected string $content)
    {
    }

    public function build(): DomNode
    {
        return $this->dom()->setTag('bdi')->children($this->content);
    }
}
