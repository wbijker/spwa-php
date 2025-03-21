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

    public function populateRow(array $row)
    {
        $ret = clone $this->instance;
        foreach ($this->columns as $name => $value) {
            $prop = &$ret->{$name};
            if ($prop instanceof Column) {
                $prop = clone $prop;
                $prop->assign($row[$name]);
            }
        }
        return $ret;
    }
}