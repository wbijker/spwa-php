<?php

namespace Spwa\UI;

/**
 * Blockquote element.
 */
class Blockquote extends UIElementContent
{
    protected ?string $cite = null;

    public function __construct(?string $content = null)
    {
        parent::__construct('blockquote');
        if ($content !== null) {
            $this->children[] = $content;
        }
    }

    public function cite(string $cite): static
    {
        $this->cite = $cite;
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('blockquote');

        if ($this->cite !== null) {
            $node->attr('cite', $this->cite);
        }

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
