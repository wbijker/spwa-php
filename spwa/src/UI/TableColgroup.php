<?php

namespace Spwa\UI;

/**
 * Table column group element.
 */
class TableColgroup
{
    /** @var TableCol[] */
    protected array $cols = [];

    public function __construct(TableCol ...$cols)
    {
        $this->cols = $cols;
    }

    public function cols(TableCol ...$cols): static
    {
        $this->cols = array_merge($this->cols, $cols);
        return $this;
    }

    public function toNode(): Node
    {
        $node = Node::el('colgroup');

        foreach ($this->cols as $col) {
            $node->children($col->toNode());
        }

        return $node;
    }
}
