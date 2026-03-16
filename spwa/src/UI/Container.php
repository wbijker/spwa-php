<?php

namespace Spwa\UI;

/**
 * Basic container element that can hold children.
 */
class Container extends UIElementContent
{
    public function __construct(string $tag = 'div')
    {
        parent::__construct($tag);
    }
}
