<?php

namespace Spwa\UI;

/**
 * Address element.
 */
class Address extends Container
{
    public function render(): Node
    {
        $node = $this->node('address');
        foreach ($this->children as $child) {
            $node->children($child->render());
        }
        return $node;
    }
}
