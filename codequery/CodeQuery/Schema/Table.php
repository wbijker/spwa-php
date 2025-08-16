<?php

namespace CodeQuery\Schema;

use CodeQuery\Columns\BoolColumn;
use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Queryable\SqlJoin;
use CodeQuery\Sources\SqlSource;
use function CodeQuery\Queryable\toExpression;

abstract class Table
{

    public function __construct(protected SqlContext $context)
    {
    }

    abstract function buildTable(TableBuilder $builder): void;


    // cache for storing joins to avoid duplication
    private $joins = [];

    /**
     * @template T
     * @param callable(T): bool $condition
     * @return T
     */
    protected function innerJoin(string $source, callable $condition)
    {
        $builder = $this->context->build($source);
        $instance = $builder->source->instance;
        $this->context->sources[$source] = $instance;

        $on = $this->context->invokeCallback($condition);
        $this->context->joins[] = new SqlJoin(
            "inner",
            $builder->source,
            toExpression($on)
        );
        return $instance;
    }


}