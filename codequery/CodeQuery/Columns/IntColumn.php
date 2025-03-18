<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\BooleanExpression;
use CodeQuery\Expressions\ConstExpression;

class IntColumn extends Column
{
    public function eq(int|BooleanExpression $value): BooleanExpression
    {
        return new BooleanExpression($this->exp, BooleanExpression::EQUAL, is_int($value) ? new ConstExpression($value) : $value);
    }
}

class IntNullableColumn extends Column
{
}