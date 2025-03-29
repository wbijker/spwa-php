<?php

namespace CodeQuery\Sources;

use CodeQuery\Queryable\SqlRootContext;

abstract class SqlSource
{
    public string $alias = "";

    abstract function setAlias(SqlRootContext $root): void;

    abstract function toSql(): string;
}

