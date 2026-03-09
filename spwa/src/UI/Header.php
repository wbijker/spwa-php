<?php

namespace Spwa\UI;

/**
 * Semantic header element.
 */
class Header extends Container
{
    public function render(): DomNode
    {
        $node = $this->node('header');
        foreach ($this->children as $child) {
            $node->children($child->render());
        }
        return $node;
    }
}
