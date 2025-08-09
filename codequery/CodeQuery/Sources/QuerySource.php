<?php

namespace CodeQuery\Sources;

use CodeQuery\Queryable\SqlQueryContext;
use CodeQuery\Queryable\SqlRootContext;

class QuerySource extends SqlSource
{
    public function __construct(private SqlQueryContext $context)
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