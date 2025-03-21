<?php

namespace CodeQuery\Sources;

abstract class TableSource extends SqlSource
{
    public function __construct(public string $tableName, public string $alias)
    {
    }

    function toSql(): string
    {
        return "`$this->tableName` AS `$this->alias`";
    }

}

