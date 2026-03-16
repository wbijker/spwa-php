<?php

namespace Spwa\UI;

/**
 * Preformatted text element.
 */
class Pre extends UIElementContent
{
    public function __construct(?string $content = null)
    {
        parent::__construct('pre');
        if ($content !== null) {
            $this->children[] = $content;
        }
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('pre');

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
