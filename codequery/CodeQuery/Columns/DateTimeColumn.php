<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\StartExpression;

class DateTimeColumn extends Column
{

    static function now(): DateTimeColumn
    {
        return new DateTimeColumn(new StartExpression());
    }

    public function convertFrom(mixed $val)
    {

    }
}