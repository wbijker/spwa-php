<?php

namespace Spwa\UI;

/**
 * Base class for all UI elements.
 *
 * @deprecated Use TagNode directly instead. UIElement is now an alias for TagNode.
 */
class UIElement extends TagNode
{
    public function __construct(string $tag = 'div')
    {
        parent::__construct($tag);
    }
}
