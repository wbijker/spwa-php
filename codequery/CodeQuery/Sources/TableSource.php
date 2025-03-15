<?php

namespace CodeQuery\Sources;

class TableSource extends SqlSource
{

    public function __construct(private string $table)
    {
    }

    function toSql(): string
    {
        return $this->table;
    }

    function alias(): string
    {
        return $this->table;
    }
}