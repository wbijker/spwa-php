<?php

namespace CodeQuery\Expressions;

class BinaryExpression implements SqlExpression
{
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