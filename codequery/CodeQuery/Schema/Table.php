<?php

namespace CodeQuery\Schema;

use CodeQuery\Queryable\SqlRootContext;
use CodeQuery\Sources\TableSource;

abstract class Table
{
    abstract protected function build(TableBuilder $builder): void;

    abstract protected function tableName(): string;

    public static function create(SqlRootContext $root): TableSource
    {
        $instance = new static();
        $tableName = $instance->tableName();
        $alias = $root->alias(strtolower($tableName[0]));

        $builder = new TableBuilder($instance, $tableName, $alias);
        $instance->build($builder);
        return $builder->source;
    }
}