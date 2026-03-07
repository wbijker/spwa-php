<?php

namespace Spwa\UI;

/**
 * Semantic aside element.
 */
class Aside extends Container
{
    public function render(): Node
    {
        $node = $this->node('aside');
        foreach ($this->children as $child) {
            $node->children($child->render());
        }
        return $node;
    }
}
