<?php

namespace Spwa\UI;

/**
 * Semantic nav element.
 */
class Nav extends Container
{
    public function render(): Node
    {
        $node = $this->node('nav');
        foreach ($this->children as $child) {
            $node->children($child->render());
        }
        return $node;
    }
}
