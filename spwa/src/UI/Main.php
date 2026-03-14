<?php

namespace Spwa\UI;

/**
 * Semantic main element.
 */
class Main extends Container
{
    public function build(): DomNode
    {
        $node = $this->dom()->setTag('main');
        foreach ($this->children as $child) {
            $node->children($child->build());
        }
        return $node;
    }
}
