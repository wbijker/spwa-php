<?php

namespace CodeQuery\Schema;

use CodeQuery\Expressions\SqlExpression;

abstract class Table
{

    public function __construct(protected SqlContext $context)
    {
    }

    abstract function buildTable(TableBuilder $builder): void;

    /**
     * @template T
     * @param callable(T): bool $condition
     * @return T
     */
    protected function innerJoin(callable $condition)
    {
        // inspect argument passed to $condition
        $reflection = new \ReflectionFunction($condition);
        $params = $reflection->getParameters();
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
        $instance = new $className();

        // then call the condition with an instance of that class
        $result = $condition($instance);
        // assert $result is of type SqlExpression
        if (!is_subclass_of($result, SqlExpression::class)) {
            throw new \InvalidArgumentException("Condition must return an instance of SqlExpression.");
        }

        echo $result->toSql();

        return $instance;
    }


}