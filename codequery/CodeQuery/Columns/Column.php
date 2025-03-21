<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\SqlExpression;

abstract class Column
{
    public function __construct(public SqlExpression $exp)
    {
    }
}