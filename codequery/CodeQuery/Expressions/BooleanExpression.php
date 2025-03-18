<?php

namespace CodeQuery\Expressions;


class BooleanExpression implements SqlExpression
{
    const EQUAL = "=";
    const NOT_EQUAL = "<>";
    const GREATER = ">";
    const GREATER_OR_EQUAL = ">=";
    const LESS = "<";
    const LESS_OR_EQUAL = "<=";

    public function __construct(
        private SqlExpression $left,
        private string        $operator,
        private SqlExpression $right
    )
    {
    }

    function toSql(): string
    {
        return "{$this->left->toSql()} {$this->operator} {$this->right->toSql()}";
    }
}