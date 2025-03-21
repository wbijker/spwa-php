<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\ConstExpression;
use CodeQuery\Expressions\SqlExpression;

abstract class Column
{
    public function __construct(public SqlExpression $exp)
    {
    }

    function assign($value) {
    }
}