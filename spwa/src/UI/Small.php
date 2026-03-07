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

    public function render(): Node
    {
        return $this->node('small')->children($this->content);
    }
}
