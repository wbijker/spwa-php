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

    public function render(): DomNode
    {
        return $this->node('bdi')->children($this->content);
    }
}
