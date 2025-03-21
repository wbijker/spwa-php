<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\BinaryExpression;
use CodeQuery\Expressions\ConstExpression;
use CodeQuery\Expressions\SqlExpression;

class FloatColumn extends Column
{
    private function toExp(float|FloatColumn $value): SqlExpression
    {
        return is_double($value)
            ? new ConstExpression($value)
            : $value->exp;
    }

    function equals(float|IntColumn $value): BoolColumn
    {
        return new BoolColumn(new BinaryExpression($this->exp, BinaryExpression::EQUAL, $this->toExp($value)));
    }

    function notEquals(float|IntColumn $value): BoolColumn
    {
        return new BoolColumn(new BinaryExpression($this->exp, BinaryExpression::NOT_EQUAL, $this->toExp($value)));
    }

    function greaterThan(float|IntColumn $value): BoolColumn
    {
        return new BoolColumn(new BinaryExpression($this->exp, BinaryExpression::GREATER, $this->toExp($value)));
    }

    function lessThan(float|IntColumn $value): BoolColumn
    {
        return new BoolColumn(new BinaryExpression($this->exp, BinaryExpression::LESS, $this->toExp($value)));
    }

    function greaterOrEqual(float|IntColumn $value): BoolColumn
    {
        return new BoolColumn(new BinaryExpression($this->exp, BinaryExpression::GREATER_OR_EQUAL, $this->toExp($value)));
    }

    function lessOrEqual(float|IntColumn $value): BoolColumn
    {
        return new BoolColumn(new BinaryExpression($this->exp, BinaryExpression::LESS_OR_EQUAL, $this->toExp($value)));
    }

    function multiply(float|IntColumn $value): IntColumn
    {
        return new IntColumn(new BinaryExpression($this->exp, BinaryExpression::MULTIPLY, $this->toExp($value)));
    }

    function divide(float|IntColumn $value): FloatColumn
    {
        return new FloatColumn(new BinaryExpression($this->exp, BinaryExpression::DIVIDE, $this->toExp($value)));
    }

    function add(float|FloatColumn $value): FloatColumn
    {
        return new FloatColumn(new BinaryExpression($this->exp, BinaryExpression::ADD, $this->toExp($value)));
    }

    function subtract(float|FloatColumn $value): FloatColumn
    {
        return new FloatColumn(new BinaryExpression($this->exp, BinaryExpression::SUBTRACT, $this->toExp($value)));
    }
}

