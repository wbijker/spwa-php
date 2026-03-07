<?php

namespace Spwa\UI;

/**
 * Horizontal rule element.
 */
class Hr extends UIElement
{
    public function render(): Node
    {
        return $this->node('hr');
    }
}
