<?php

namespace CodeQuery\Expressions;

class StarExpression implements SqlExpression
{
    function toSql(): string
    {
        return "*";
    }

    function replace(SqlExpression $seek, SqlExpression $replace): SqlExpression
    {
        if ($this === $seek) {
            return $replace;
        }
        return $this;
    }
}