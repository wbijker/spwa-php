<?php

namespace Spwa\UI;

/**
 * Description details element.
 */
class Dd extends UIElementContent
{
    public function __construct(string|UIElement|null $content = null)
    {
        parent::__construct('dd');
        if ($content !== null) {
            $this->children[] = $content;
        }
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('dd');

        foreach ($this->children as $child) {
            if ($child instanceof UIElement) {
                $node->children($child->build());
            } else {
                $node->children($child);
            }
        }

        return $node;
    }
}
