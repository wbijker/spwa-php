<?php

namespace CodeQuery\Columns;

class TableBuilder
{


    public function __construct(private string $table)
    {
    }

    function int(string $column,
                 bool   $primaryKey = false,
                 bool   $autoIncrement = false
    ): IntColumnDefinition
    {
        return new IntColumnDefinition(
            column: $column,
            table: $this->table,
            primaryKey: $primaryKey,
            autoIncrement: $autoIncrement
        );
    }

    function intNullable(string $column,
                         bool   $primaryKey = false
    ): IntNullableColumnDefinition
    {
        return new IntNullableColumnDefinition(
            column: $column,
            table: $this->table,
            primaryKey: $primaryKey,
        );
    }

    function string(string $column,
                    bool   $primaryKey = false,
                    bool   $unique = false
    ): StringColumnDefinition
    {
        return new StringColumnDefinition(
            column: $column,
            table: $this->table,
            primaryKey: $primaryKey,
            unique: $unique,
        );
    }

    function stringNullable(string $column,
                            bool   $primaryKey = false
    ): StringNullableColumnDefinition
    {
        return new StringNullableColumnDefinition(
            column: $column,
            table: $this->table,
            primaryKey: $primaryKey,
        );
    }

    function bool(string $column): BoolColumnDefinition
    {
        return new BoolColumnDefinition(
            column: $column,
            table: $this->table,
        );
    }

    function boolNullable(string $column): BoolNullableColumnDefinition
    {
        return new BoolNullableColumnDefinition(
            column: $column,
            table: $this->table,
        );
    }

    function float(string $column): FloatColumnDefinition
    {
        return new FloatColumnDefinition(
            column: $column,
            table: $this->table
        );
    }

    function floatNullable(string $column,
                           bool   $primaryKey = false
    ): FloatNullableColumnDefinition
    {
        return new FloatNullableColumnDefinition(
            column: $column,
            table: $this->table
        );
    }

}

