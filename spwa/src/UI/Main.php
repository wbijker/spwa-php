<?php

namespace Spwa\UI;

/**
 * Semantic main element.
 */
class Main extends Container
{
    public function render(): DomNode
    {
        $node = $this->node('main');
        foreach ($this->children as $child) {
            $node->children($child->render());
        }
        return $node;
    }
}
