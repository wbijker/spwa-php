<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\ColumnExpression;

class FloatNullableColumnDefinition extends StringColumn
{

    /**
     * @param string $column
     * @param string $table
     * @param float|null $default
     * @param bool $unique
     * @param bool $index
     */
    public function __construct(
        public string $column,
        public string $table,
        public ?float $default = null,
        public bool   $unique = false,
        public bool   $index = false)
    {
        parent::__construct(new ColumnExpression($column, $table));
    }
}