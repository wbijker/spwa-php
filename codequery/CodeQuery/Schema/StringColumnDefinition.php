<?php

namespace CodeQuery\Schema;

use CodeQuery\Columns\StringColumn;
use CodeQuery\Expressions\ColumnExpression;
use CodeQuery\Schema\Traits\ForeignKeyTrait;
use CodeQuery\Schema\Traits\NullableTrait;
use CodeQuery\Schema\Traits\PrimaryKeyTrait;
use CodeQuery\Schema\Traits\UniqueTrait;
use CodeQuery\Sources\TableSource;

class StringColumnDefinition extends StringColumn implements ColumnDefinition
{
    use ForeignKeyTrait;
    use NullableTrait;
    use PrimaryKeyTrait;
    use UniqueTrait;

    public function __construct(
        string      $column,
        TableSource $source
    )
    {
        parent::__construct(new ColumnExpression($column, $source));
    }

    function buildSchema(): string
    {
        return "";
    }
}