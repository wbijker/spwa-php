<?php

namespace Spwa\UI;

/**
 * Semantic nav element.
 */
class Nav extends Container
{
    public function render(): DomNode
    {
        $node = $this->node('nav');
        foreach ($this->children as $child) {
            $node->children($child->render());
        }
        return $node;
    }
}
