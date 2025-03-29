<?php

namespace CodeQuery\Queryable;

use CodeQuery\Columns\IntColumn;
use CodeQuery\Expressions\StartExpression;

class WindowFunctions
{
    /*
     * $windows = WindowFunctions::rowNumber()
        ->partitionBy($p->id)
        ->orderBy($p->price);
    */
    static function rowNumber($partitionBy, $orderBy): IntColumn
    {
        return new IntColumn(new StartExpression());
    }
}