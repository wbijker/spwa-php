<?php

namespace CodeQuery\Expressions;

use CodeQuery\Sources\TableSource;

class ColumnExpression implements SqlExpression
{
    public function __construct(private string $column, private TableSource $source)
    {
    }

    function toSql(): string
    {
        return "{$this->source->alias}.{$this->column}";
    }
}
