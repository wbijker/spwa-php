<?php

namespace CodeQuery\Expressions;

class ConstExpression implements SqlExpression
{
    public function __construct(private mixed $value)
    {
    }

    function toSql(): string
    {
        return match (gettype($this->value)) {
            "string" => $this->value, // escape
            "integer", "double" => (string)$this->value,
            "boolean" => $this->value ? "1" : "0",
            default => "NULL",
        };
    }
}

