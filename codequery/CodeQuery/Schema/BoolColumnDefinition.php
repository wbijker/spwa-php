<?php

namespace CodeQuery\Schema;

use CodeQuery\Columns\BoolColumn;
use CodeQuery\Expressions\ColumnExpression;
use CodeQuery\Schema\Traits\NullableTrait;
use CodeQuery\Sources\TableSource;

class BoolColumnDefinition extends BoolColumn implements ColumnDefinition
{
    use NullableTrait;

    public function __construct(
        private string $column,
        Table    $table
    )
    {
        parent::__construct(new ColumnExpression($column, $table->getSource()));
    }


    function buildSchema(): string
    {
        return "";
    }
}