<?php

namespace Spwa\UI;

/**
 * Semantic aside element.
 */
class Aside extends Container
{
    public function build(): DomNode
    {
        $node = $this->dom()->setTag('aside');
        foreach ($this->children as $child) {
            $node->children($child->build());
        }
        return $node;
    }
}
