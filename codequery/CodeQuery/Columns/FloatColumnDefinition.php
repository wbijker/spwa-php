<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\ColumnExpression;

class FloatColumnDefinition extends FloatColumn
{

    /**
     * @param string $column
     * @param string $table
     * @param float $default
     * @param bool $unique
     * @param bool $index
     */
    public function __construct(
        public string $column,
        public string $table,
        public float  $default = 0,
        public bool   $unique = false,
        public bool   $index = false)
    {
        parent::__construct(new ColumnExpression($column, $table));
    }
}


