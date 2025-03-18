<?php

namespace CodeQuery;

use CodeQuery\Columns\TableBuilder;
use CodeQuery\Queryable\Queryable;
use CodeQuery\Sources\TableSource;

class DbContext
{

    /**
     * @param class-string $table
     * @return Queryable
     * @throws \Exception
     */
    function from(string $table): Queryable
    {
        $source = new $table();
        if (!$source instanceof TableSource) {
            throw new \Exception("Table must be an instance of TableSource");
        }
        $source->create(new TableBuilder($source->tableName()));
        return new Queryable($source);
    }

}

