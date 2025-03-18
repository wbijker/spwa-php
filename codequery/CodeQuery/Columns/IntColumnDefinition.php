<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\ColumnExpression;

class IntColumnDefinition extends IntColumn
{

    /**
     * @param string $column
     * @param string $table
     * @param bool $primaryKey
     * @param bool $autoIncrement
     * @param int $default
     * @param bool $unique
     * @param bool $index
     */
    public function __construct(
        public string $column,
        public string $table,
        public bool   $primaryKey = false,
        public bool   $autoIncrement = false,
        public int    $default = 0,
        public bool   $unique = false,
        public bool   $index = false)
    {
        parent::__construct(new ColumnExpression($column, $table));
    }

    function foreignKey($callback): IntColumnDefinition
    {
        return $this;
    }
}


