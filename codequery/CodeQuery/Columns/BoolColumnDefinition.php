<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\ColumnExpression;

class BoolColumnDefinition extends BoolColumn
{

    /**
     * @param string $column
     * @param string $table
     * @param bool $default
     */
    public function __construct(
        public string $column,
        public string $table,
        public bool   $default = false
    )
    {
        parent::__construct(new ColumnExpression($column, $table));
    }
}