<?php

namespace Spwa\UI;

/**
 * Table row (tr).
 */
class TableRow extends UIElementContent
{
    public function __construct(TableCell|TableHeading ...$cells)
    {
        parent::__construct('tr');
        foreach ($cells as $cell) {
            $this->content($cell);
        }
    }

    /**
     * Add cells to the row.
     */
    public function cells(TableCell|TableHeading ...$cells): static
    {
        foreach ($cells as $cell) {
            $this->content($cell);
        }
        return $this;
    }
}
