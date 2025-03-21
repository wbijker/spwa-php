<?php

namespace CodeQuery\Schema;

abstract class Table
{
    abstract protected function build(TableBuilder $builder): void;

    abstract protected function tableName(): string;

    public static function create(): TableBuilder
    {
        $instance = new static();
        $builder = new TableBuilder($instance, $instance->tableName());
        $instance->build($builder);
        return $builder;
    }
}