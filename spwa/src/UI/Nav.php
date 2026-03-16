<?php

namespace Spwa\UI;

/**
 * Semantic nav element.
 */
class Nav extends Container
{
    public function __construct()
    {
        parent::__construct('nav');
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('nav');
        foreach ($this->children as $child) {
            $node->children($child->build());
        }
        return $node;
    }
}
