<?php

namespace CodeQuery\Schema;


class TableBuilder
{
    public string $tableName;

    public function __construct()
    {
    }
    
    function int(string $column): IntColumnDefinition
    {
        return new IntColumnDefinition($column, $this->tableName);
    }

    function string(string $column): StringColumnDefinition
    {
        return new StringColumnDefinition($column, $this->tableName);
    }

    function bool(string $column): BoolColumnDefinition
    {
        return new BoolColumnDefinition($column, $this->tableName);
    }

    function float(string $column): FloatColumnDefinition
    {
        return new FloatColumnDefinition($column, $this->tableName);
    }

}

