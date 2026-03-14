<?php

namespace Spwa\UI;

/**
 * Semantic article element.
 */
class Article extends Container
{
    public function build(): DomNode
    {
        $node = $this->dom()->setTag('article');
        foreach ($this->children as $child) {
            $node->children($child->build());
        }
        return $node;
    }
}
