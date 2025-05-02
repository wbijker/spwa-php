<?php

namespace CodeQuery\Columns;

use CodeQuery\Expressions\StarExpression;

class DateTimeColumn extends Column
{

    static function now(): DateTimeColumn
    {
        return new DateTimeColumn(new StarExpression());
    }

    public function convertFrom(mixed $val)
    {

    }
}