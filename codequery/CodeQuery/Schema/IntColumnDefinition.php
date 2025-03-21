<?php

namespace CodeQuery\Schema;

use CodeQuery\Columns\IntColumn;
use CodeQuery\Expressions\ColumnExpression;
use CodeQuery\Schema\Traits\ForeignKeyTrait;
use CodeQuery\Schema\Traits\NullableTrait;
use CodeQuery\Schema\Traits\PrimaryKeyTrait;
use CodeQuery\Schema\Traits\UniqueTrait;


class IntColumnDefinition extends IntColumn implements ColumnDefinition
{
    use ForeignKeyTrait;
    use NullableTrait;
    use PrimaryKeyTrait;
    use UniqueTrait;

    protected bool $autoIncrement = false;

    function autoIncrement(): static
    {
        $this->autoIncrement = true;
        return $this;
    }

    public function __construct(
        private string $column,
        string         $table
    )
    {
        parent::__construct(new ColumnExpression($column, $table));
    }

    // add FK

    function buildSchema(): string
    {
        $schema = "{$this->column} INT";

        if ($this->primaryKey) {
            $schema .= " PRIMARY KEY";
        }

        if ($this->autoIncrement) {
            $schema .= " AUTO_INCREMENT";
        }

        return $schema;
    }
}
