<?php

namespace Spwa\UI;

/**
 * Table column element.
 */
class TableCol
{
    protected ?int $span = null;
    protected ?string $width = null;

    public function span(int $span): static
    {
        $this->span = $span;
        return $this;
    }

    public function width(string $width): static
    {
        $this->width = $width;
        return $this;
    }

    public function toNode(): Node
    {
        $node = Node::el('col');

        if ($this->span !== null) {
            $node->attr('span', (string)$this->span);
        }

        if ($this->width !== null) {
            $node->attr('width', $this->width);
        }

        return $node;
    }
}
