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

    public function render(): Node
    {
        return $this->node('bdi')->children($this->content);
    }
}
