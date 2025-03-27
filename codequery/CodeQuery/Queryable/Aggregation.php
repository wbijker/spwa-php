<?php

namespace CodeQuery\Queryable;

use CodeQuery\Columns\IntColumn;
use CodeQuery\Expressions\StartExpression;


class Aggregation
{

    static function star(): IntColumn
    {
        return new IntColumn(new StartExpression());
    }

}