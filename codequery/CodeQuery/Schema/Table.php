<?php

namespace CodeQuery\Schema;

use CodeQuery\Columns\BoolColumn;
use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Queryable\SqlJoin;

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
    protected function innerJoin(callable $condition)
    {
        // inspect argument passed to $condition
        $r = new \ReflectionFunction($condition);

        // cahe function based on file and line number only
        $key = $r->getStartLine();
        $hit = $this->joins[$key] ?? null;
        if ($hit) {
            // if we have a hit, return the instance
            return $hit;
        }

        $params = $r->getParameters();
        if (count($params) !== 1) {
            throw new \InvalidArgumentException("Condition must accept exactly one parameter.");
        }
        $type = $params[0]->getType();
        if (!$type || $type->isBuiltin()) {
            throw new \InvalidArgumentException("Condition parameter must be a class type.");
        }
        $className = $type->getName();
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Class $className does not exist.");
        }
        if (!is_subclass_of($className, self::class)) {
            throw new \InvalidArgumentException("Class $className must be a Table.");
        }

        $builder = $this->context->build($className);

        $instance = $builder->source->instance;
        // then call the condition with an instance of that class
        $result = $condition($instance);

        if (get_class($result) != BoolColumn::class) {
            throw new \InvalidArgumentException("Condition must return an instance of BoolColumn.");
        }

        // source is $instance->source, join is inner and condition is $result
        $this->context->joins[] = new SqlJoin("inner", $builder->source, $result->exp);

        // add to the join cache
        $this->joins[$key] = $instance;
        // return the joining instance
        return $instance;
    }


}