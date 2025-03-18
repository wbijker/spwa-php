<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\SqlExpression;

abstract class Column implements SqlExpression
{
    public function __construct(protected SqlExpression $exp)
    {
    }

    function toSql(): string
    {
        return $this->exp->toSql();
    }

}