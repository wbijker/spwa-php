<?php

namespace CodeQuery\Schema;

use CodeQuery\Columns\BoolColumn;
use CodeQuery\Expressions\ColumnExpression;
use CodeQuery\Schema\Traits\NullableTrait;

class BoolColumnDefinition extends BoolColumn implements ColumnDefinition
{
    use NullableTrait;

    public function __construct(
        private string $column,
        string         $table
    )
    {
        parent::__construct(new ColumnExpression($column, $table));
    }


    function buildSchema(): string
    {
        return "";
    }
}