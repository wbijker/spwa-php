<?php

namespace CodeQuery\Expressions;

class AliasExpression implements SqlExpression
{
    public function __construct(public SqlExpression $expr, public string $alias)
    {
    }

    function toSql(): string
    {
        return $this->expr->toSql() . " AS " . $this->alias;
    }

    function replace(SqlExpression $seek, SqlExpression $replace): SqlExpression
    {
        if ($this === $seek)
            return $replace;

        return $this->expr->replace($seek, $replace);
    }
}