<?php

namespace CodeQuery\Expressions;

use CodeQuery\Schema\Table;

class ColumnExpression implements SqlExpression
{
    public function __construct(private string $column, private Table $table)
    {
    }

    function toSql(): string
    {
        return "{$this->table->getSource()->alias}.{$this->column}";
    }
}
