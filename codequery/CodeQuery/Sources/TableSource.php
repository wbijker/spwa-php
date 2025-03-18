<?php

namespace CodeQuery\Sources;

use CodeQuery\Columns\TableBuilder;

abstract class TableSource extends SqlSource
{

    function toSql(): string
    {
        return $this->tableName();
    }

    function alias(): string
    {
        return $this->tableName();
    }

    abstract function create(TableBuilder $builder);

    abstract function tableName(): string;


}

