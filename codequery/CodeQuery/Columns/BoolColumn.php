<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\BinaryExpression;
use CodeQuery\Expressions\ConstExpression;
use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Expressions\UnaryExpression;

class BoolColumn extends Column
{
    private function toExp(bool|BoolColumn $value): SqlExpression
    {
        return is_bool($value)
            ? new ConstExpression($value)
            : $value->exp;
    }

    function isTrue(): BoolColumn
    {
        return $this;
    }

    function isFalse(): BoolColumn
    {
        return new BoolColumn(new UnaryExpression($this->exp, UnaryExpression::NOT));
    }

    function and(bool|BoolColumn $value): BoolColumn
    {
        return new BoolColumn(new BinaryExpression($this->exp, BinaryExpression::AND, $this->toExp($value)));
    }

    function or(bool|BoolColumn $value): BoolColumn
    {
        return new BoolColumn(new BinaryExpression($this->exp, BinaryExpression::OR, $this->toExp($value)));
    }
}

