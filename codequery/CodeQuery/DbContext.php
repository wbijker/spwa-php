<?php

namespace CodeQuery;

use CodeQuery\Queryable\Queryable;
use CodeQuery\Schema\Table;
use CodeQuery\Schema\TableBuilder;
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
        /** @var Table $table */
        $builder = $table::create();
        $source = new TableSource($builder, "");
        return new Queryable($source);
    }

}

