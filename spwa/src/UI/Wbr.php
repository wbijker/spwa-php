<?php

namespace Spwa\UI;

/**
 * Word break opportunity element.
 */
class Wbr extends UIElement
{
    public function render(): DomNode
    {
        return $this->node('wbr');
    }
}
