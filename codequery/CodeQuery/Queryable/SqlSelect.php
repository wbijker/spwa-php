<?php

namespace CodeQuery\Queryable;

use CodeQuery\Columns\Column;
use CodeQuery\Expressions\SqlExpression;

class SqlSelect
{
    /**
     * @var SqlExpression[] $select
     */
    public array $columns = [];
    public $instance = null;

    /**
     * @param SqlExpression[] $columns
     * @param null $instance
     */
    public function __construct(array $columns, $instance)
    {
        $this->columns = $columns;
        $this->instance = $instance;
    }

    public function populateRow(array $row, $obj): void
    {
        foreach ($this->columns as $name => $value) {
            $prop = is_array($this->instance)
                ? $this->instance[$name]
                : $this->instance->$name;

            if ($prop instanceof Column) {
                $obj->$name = $prop->convertFrom($row[$name]);
            }
        }
    }
}