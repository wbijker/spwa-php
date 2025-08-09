<?php

namespace CodeQuery\Schema;

use CodeQuery\Sources\TableSource;

class TableBuilder
{
    public function __construct(protected Table $table)
    {
    }

    function getSource(): TableSource
    {
        return new TableSource($this->tableName, $this->table);
    }

    private ?string $tableName = null;

    function tableName(string $name): void
    {
        $this->tableName = $name;
    }

    function int(string $column): IntColumnDefinition
    {
        return new IntColumnDefinition($column, $this->table);
    }

    function string(string $column): StringColumnDefinition
    {
        return new StringColumnDefinition($column, $this->table);
    }

    function bool(string $column): BoolColumnDefinition
    {
        return new BoolColumnDefinition($column, $this->table);
    }

    function float(string $column): FloatColumnDefinition
    {
        return new FloatColumnDefinition($column, $this->table);
    }
}