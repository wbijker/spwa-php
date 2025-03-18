<?php

namespace CodeQuery\Queryable;

use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Columns\SqlSource;

class SqlJoin
{
    public function __construct(
        public string        $type,
        public SqlSource     $source,
        public SqlExpression $on
    )
    {
    }
}