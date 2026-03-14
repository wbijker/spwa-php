<?php

namespace Spwa\UI;

/**
 * Word break opportunity element.
 */
class Wbr extends UIElement
{
    public function build(): DomNode
    {
        return $this->dom()->setTag('wbr');
    }
}
