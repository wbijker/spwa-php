<?php

namespace CodeQuery\Queryable;

class SqlRootContext
{
    private int $count = 0;

    function alias(): string
    {
        return 'q' . $this->count++;
    }
}