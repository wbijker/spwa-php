<?php

namespace Spwa\UI;

/**
 * `grid-auto-flow` — how auto-placed items fill the grid. The backing value
 * is the class-name token; css() expands the "dense" pairs to a space.
 */
enum GridFlow: string
{
    case Row = 'row';
    case Column = 'column';
    case Dense = 'dense';
    case RowDense = 'row-dense';
    case ColumnDense = 'column-dense';

    /** CSS value (e.g. RowDense -> "row dense"). */
    public function css(): string
    {
        return str_replace('-', ' ', $this->value);
    }
}
