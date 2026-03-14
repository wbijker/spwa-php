<?php

namespace Spwa\UI;

/**
 * Address element.
 */
class Address extends Container
{
    public function build(): DomNode
    {
        $node = $this->dom()->setTag('address');
        foreach ($this->children as $child) {
            $node->children($child->build());
        }
        return $node;
    }
}
