<?php

namespace CodeQuery\Sources;

use CodeQuery\Queryable\SqlContext;
use CodeQuery\Queryable\SqlRootContext;

class QuerySource extends SqlSource
{
    public function __construct(private SqlContext $context)
    {
    }

    function setAlias(SqlRootContext $root): void
    {
        $this->alias = $root->alias("q");
    }

    function toSql(): string
    {
        return "({$this->context->toSql()}) {$this->alias}";
    }

}