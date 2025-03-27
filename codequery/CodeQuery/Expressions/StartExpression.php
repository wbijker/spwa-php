<?php

namespace CodeQuery\Expressions;

class StartExpression implements SqlExpression
{
    function toSql(): string
    {
        return "*";
    }
}