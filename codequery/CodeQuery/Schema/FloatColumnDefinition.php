<?php

namespace CodeQuery\Schema;

use CodeQuery\Columns\FloatColumn;
use CodeQuery\Expressions\ColumnExpression;
use CodeQuery\Schema\Traits\NullableTrait;
use CodeQuery\Sources\TableSource;

class FloatColumnDefinition extends FloatColumn implements ColumnDefinition
{
    use NullableTrait;

    public function __construct(
        string      $column,
        TableSource $table
    )
    {
        parent::__construct(new ColumnExpression($column, $table));
    }


    function buildSchema(): string
    {
        return "";
    }
}