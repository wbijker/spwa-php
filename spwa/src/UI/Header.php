<?php

namespace Spwa\UI;

/**
 * Semantic header element.
 */
class Header extends Container
{
    public function __construct()
    {
        parent::__construct('header');
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('header');
        foreach ($this->children as $child) {
            $node->children($child->build());
        }
        return $node;
    }
}
