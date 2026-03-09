<?php

namespace Spwa\UI;

/**
 * Semantic article element.
 */
class Article extends Container
{
    public function render(): DomNode
    {
        $node = $this->node('article');
        foreach ($this->children as $child) {
            $node->children($child->render());
        }
        return $node;
    }
}
