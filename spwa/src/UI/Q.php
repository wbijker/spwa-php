<?php

namespace Spwa\UI;

/**
 * Inline quote element.
 */
class Q extends UIElement
{
    protected ?string $cite = null;

    public function __construct(protected string $content)
    {
    }

    public function cite(string $cite): static
    {
        $this->cite = $cite;
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('q')->children($this->content);

        if ($this->cite !== null) {
            $node->attr('cite', $this->cite);
        }

        return $node;
    }
}
