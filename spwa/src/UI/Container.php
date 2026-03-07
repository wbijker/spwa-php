<?php

namespace Spwa\UI;

/**
 * Basic container element that can hold children.
 */
class Container extends UIElement
{
    public function __construct()
    {
        parent::__construct('div');
    }
}
