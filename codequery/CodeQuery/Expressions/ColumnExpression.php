<?php

namespace CodeQuery\Expressions;

class ColumnExpression implements SqlExpression
{
    public function __construct(private string $column, private string $table)
    {
    }

    function toSql(): string
    {
        return "{$this->column}";
    }
}