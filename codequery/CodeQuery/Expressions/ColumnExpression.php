<?php

namespace CodeQuery\Expressions;

class ColumnExpression implements SqlExpression
{
    public function __construct(private string $column, private string $alias)
    {
    }

    function toSql(): string
    {
        return "{$this->alias}.{$this->column}";
    }
}