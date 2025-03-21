<?php

namespace CodeQuery\Sources;

use CodeQuery\Schema\TableBuilder;

class TableSource extends SqlSource
{
    public function __construct(private TableBuilder $table, public string $alias)
    {
    }

    function toSql(): string
    {
        return "`{$this->table->tableName} tableName` AS `$this->alias`";
    }

    function getInstance()
    {
        return $this->table->instance;
    }

}

