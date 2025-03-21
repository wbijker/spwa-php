<?php

namespace CodeQuery\Schema;


use CodeQuery\Sources\TableSource;

class TableBuilder
{

    public TableSource $source;

    public function __construct(Table $instance, string $tableName, string $alias)
    {
        $this->source = new TableSource($alias, $tableName, $instance);
    }

    function int(string $column): IntColumnDefinition
    {
        return new IntColumnDefinition($column, $this->source);
    }

    function string(string $column): StringColumnDefinition
    {
        return new StringColumnDefinition($column, $this->source);
    }

    function bool(string $column): BoolColumnDefinition
    {
        return new BoolColumnDefinition($column, $this->source);
    }

    function float(string $column): FloatColumnDefinition
    {
        return new FloatColumnDefinition($column, $this->source);
    }

}

