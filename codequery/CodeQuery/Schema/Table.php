<?php

namespace CodeQuery\Schema;

use CodeQuery\Sources\TableSource;

abstract class Table
{
    private TableSource $source;

    public function __construct(protected string $tableName)
    {
        $this->source = new TableSource($this->tableName, $this);
    }

    function getSource(): TableSource
    {
        return $this->source;
    }

    protected function int(string $column): IntColumnDefinition
    {
        return new IntColumnDefinition($column, $this);
    }

    protected function string(string $column): StringColumnDefinition
    {
        return new StringColumnDefinition($column, $this);
    }

    protected function bool(string $column): BoolColumnDefinition
    {
        return new BoolColumnDefinition($column, $this);
    }

    protected function float(string $column): FloatColumnDefinition
    {
        return new FloatColumnDefinition($column, $this);
    }
}