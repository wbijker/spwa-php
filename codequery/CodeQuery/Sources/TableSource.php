<?php

namespace CodeQuery\Sources;

use CodeQuery\Queryable\SqlRootContext;

class TableSource extends SqlSource
{

    public function __construct(
        public string $tableName,
        public        $instance
    )
    {
    }

    function setAlias(SqlRootContext $root): void
    {
        $this->alias = $root->alias(strtolower($this->tableName[0]));
    }

    function toSql(): string
    {
        return "`{$this->tableName}` {$this->alias}";
    }

}

