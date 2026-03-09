<?php

namespace Spwa\UI;

/**
 * Semantic footer element.
 */
class Footer extends Container
{
    public function render(): DomNode
    {
        $node = $this->node('footer');
        foreach ($this->children as $child) {
            $node->children($child->render());
        }
        return $node;
    }
}
