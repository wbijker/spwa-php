<?php

namespace Spwa\UI;

/**
 * Generic div element.
 */
class Div extends Container
{
    public function render(): Node
    {
        $node = $this->node('div');

        foreach ($this->children as $child) {
            $node->children($child->render());
        }

        return $node;
    }
}
