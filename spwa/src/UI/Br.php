<?php

namespace Spwa\UI;

/**
 * Line break element.
 */
class Br extends UIElement
{
    public function render(): Node
    {
        return $this->node('br');
    }
}
