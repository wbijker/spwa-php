<?php

namespace Spwa\UI;

/**
 * Address element.
 */
class Address extends Container
{
    public function render(): DomNode
    {
        $node = $this->node('address');
        foreach ($this->children as $child) {
            $node->children($child->render());
        }
        return $node;
    }
}
