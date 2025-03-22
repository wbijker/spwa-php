<?php

namespace CodeQuery\Schema;

use CodeQuery\Columns\FloatColumn;
use CodeQuery\Expressions\ColumnExpression;
use CodeQuery\Schema\Traits\NullableTrait;

class FloatColumnDefinition extends FloatColumn implements ColumnDefinition
{
    use NullableTrait;

    public function __construct(
        string      $column,
        Table $table
    )
    {
        parent::__construct(new ColumnExpression($column, $table));
    }


    function buildSchema(): string
    {
        return "";
    }
}