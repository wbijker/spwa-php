<?php

namespace Spwa\UI;

/**
 * Word break opportunity element.
 */
class Wbr extends UIElement
{
    public function render(): Node
    {
        return $this->node('wbr');
    }
}
