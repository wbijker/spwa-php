<?php

namespace CodeQuery\Sources;

use CodeQuery\Schema\TableBuilder;

class TableSource extends SqlSource
{

    public function __construct(
        public string       $alias,
        public string       $tableName,
        public              $instance
    )
    {

    }

    function toSql(): string
    {
        return "`{$this->tableName}` {$this->alias}";
    }

    function getInstance()
    {
        return $this->instance;
    }

}

