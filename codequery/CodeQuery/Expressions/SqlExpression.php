<?php

namespace CodeQuery\Expressions;

interface SqlExpression
{
    function toSql(): string;
    function replace(SqlExpression $seek, SqlExpression $replace): SqlExpression;
}

