<?php

namespace Spwa\UI;

/**
 * Table row element.
 */
class TableRow extends UIElement
{
    /** @var (TableCell|TableHeading)[] */
    protected array $cells = [];

    public function __construct(TableCell|TableHeading ...$cells)
    {
        $this->cells = $cells;
    }

    /**
     * Add cells to the row.
     */
    public function cells(TableCell|TableHeading ...$cells): static
    {
        $this->cells = array_merge($this->cells, $cells);
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('tr');

        foreach ($this->cells as $cell) {
            $node->children($cell->render());
        }

        return $node;
    }
}
