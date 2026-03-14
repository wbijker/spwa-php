<?php

namespace Spwa\UI;

/**
 * Semantic footer element.
 */
class Footer extends Container
{
    public function build(): DomNode
    {
        $node = $this->dom()->setTag('footer');
        foreach ($this->children as $child) {
            $node->children($child->build());
        }
        return $node;
    }
}
