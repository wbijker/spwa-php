<?php

namespace Spwa\UI;

/**
 * Description term element.
 */
class Dt extends UIElementContent
{
    public function __construct(string|UIElement|null $content = null)
    {
        parent::__construct('dt');
        if ($content !== null) {
            $this->children[] = $content;
        }
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('dt');

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
