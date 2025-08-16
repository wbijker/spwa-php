<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\BinaryExpression;
use CodeQuery\Expressions\ConstExpression;
use CodeQuery\Expressions\SqlExpression;

class StringColumn extends Column
{

    public function createAlias(SqlExpression $exp): static
    {
        return new StringColumn($exp);
    }

    private function toExp(string|StringColumn $value): SqlExpression
    {
        return is_string($value)
            ? new ConstExpression($value)
            : $value->exp;
    }

    function equals(string|StringColumn $value): StringColumn
    {
        return new StringColumn(new BinaryExpression($this->exp, BinaryExpression::EQUAL, $this->toExp($value)));
    }

    function notEquals(string|StringColumn $value): StringColumn
    {
        return new StringColumn(new BinaryExpression($this->exp, BinaryExpression::NOT_EQUAL, $this->toExp($value)));
    }

    public string $value;
    function convertFrom(mixed $val): void
    {
        $this->value = (string)$val;
    }
}

