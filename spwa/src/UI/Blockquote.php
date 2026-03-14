<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

/**
 * Blockquote element.
 */
class Blockquote extends UIElement
{
    protected ?string $cite = null;
    /** @var (DomNode|VNode|string)[] */
    protected array $children = [];

    public function __construct(?string $content = null)
    {
        if ($content !== null) {
            $this->children[] = $content;
        }
    }

    public function content(DomNode|VNode|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
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
