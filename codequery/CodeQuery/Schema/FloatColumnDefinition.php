<?php

namespace CodeQuery\Schema;

use CodeQuery\Columns\FloatColumn;
use CodeQuery\Expressions\ColumnExpression;
use CodeQuery\Schema\Traits\NullableTrait;

class FloatColumnDefinition extends FloatColumn implements ColumnDefinition
{
    use NullableTrait;

    public function __construct(
        string $column,
        string $table
    )
    {
        parent::__construct(new ColumnExpression($column, $table));
    }


    function buildSchema(): string
    {
        return "";
    }
}