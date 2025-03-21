<?php

namespace CodeQuery\Expressions;

class ColumnExpression implements SqlExpression
{
    public function __construct(private string $column, private string $prefix)
    {
    }

    function toSql(): string
    {
        return "{$this->prefix}.{$this->column}";
    }
}