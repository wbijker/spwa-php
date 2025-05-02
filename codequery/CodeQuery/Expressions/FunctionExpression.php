<?php

namespace CodeQuery\Expressions;

class FunctionExpression implements SqlExpression
{
    /**
     * @param string $name
     * @param SqlExpression[] $params
     */
    public function __construct(private string $name, private array $params)
    {
    }

    function toSql(): string
    {
        return $this->name . '(' . implode(", ", array_map(fn($e) => $e->toSql(), $this->params)) . ")";
    }

    function replace(SqlExpression $seek, SqlExpression $replace): SqlExpression
    {
        if ($this === $seek) {
            return $replace;
        }
        return new FunctionExpression($this->name, array_map(fn($e) => $e->replace($seek, $replace), $this->params));
    }
}