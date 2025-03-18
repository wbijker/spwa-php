<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\ColumnExpression;

class IntNullableColumnDefinition extends IntNullableColumn
{

    /**
     * @param string $column
     * @param string $table
     * @param bool $primaryKey
     * @param int|null $default
     * @param bool $unique
     * @param bool $index
     */
    public function __construct(
        public string $column,
        public string $table,
        public bool   $primaryKey = false,
        public ?int   $default = null,
        public bool   $unique = false,
        public bool   $index = false)
    {
        parent::__construct(new ColumnExpression($column, $table));
    }
}