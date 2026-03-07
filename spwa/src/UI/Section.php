<?php

namespace Spwa\UI;

/**
 * Semantic section element.
 */
class Section extends Container
{
    public function render(): Node
    {
        $node = $this->node('section');
        foreach ($this->children as $child) {
            $node->children($child->render());
        }
        return $node;
    }
}
