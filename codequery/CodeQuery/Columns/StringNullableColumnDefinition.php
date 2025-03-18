<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\ColumnExpression;

class StringNullableColumnDefinition extends StringNullableColumn
{

    /**
     * @param string $column
     * @param string $table
     * @param bool $primaryKey
     * @param bool $notNull
     * @param string|null $default
     * @param bool $unique
     * @param bool $index
     */
    public function __construct(
        public string  $column,
        public string  $table,
        public bool    $primaryKey = false,
        public bool    $notNull = false,
        public ?string $default = null,
        public bool    $unique = false,
        public bool    $index = false)
    {
        parent::__construct(new ColumnExpression($column, $table));
    }

}
