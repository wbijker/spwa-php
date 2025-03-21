<?php

namespace CodeQuery\Queryable;

use CodeQuery\Columns\Column;
use CodeQuery\Expressions\AliasExpression;
use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Sources\SqlSource;
use Exception;
use ReflectionFunction;
use ReflectionNamedType;

class Queryable
{
    private SqlContext $context;

    public function __construct(SqlSource $source)
    {
        $this->context = new SqlContext($source, new SqlRootContext());
    }


    /**
     * @param callable $callback
     * @return $this
     * @throws Exception
     */
    function select(callable $callback): Queryable
    {
        // 1: Run the factory.
        $ret = $this->invokeCallback($callback);
        if (!is_object($ret)) {
            throw new Exception("Select must return an object");
        }

        // 2: Trace all members. Inspect members of Selection: ($p->id, $p->name, $p->price->multiply(2))
        $vars = get_object_vars($ret);

        // 3: Alias members per selection. Select p.id as c1, p.name as c2, p.price * 2 as c3
        $select = [];

        foreach ($vars as $key => $value) {
            if ($value instanceof SqlExpression) {
                $select[] = new AliasExpression($value, $key);
            }
        }

        // 4: Execute SQL: [ ['c1' => 1, 'c2' => 'P1', 'c3' => 12.12], ['c1' => 2, 'c2' => 'P2', 'c3' => 43.4] ]
        // 5: Selector factory.
        /*  $reflection = new ReflectionClass(Product::class);
            $s = $reflection->newInstanceWithoutConstructor();
            $s->id = $row['c1']; $s->name = $row['c2']; $s->price = $row['c3'];*/


        $this->context->select = $select;
        return $this;
    }

    /**
     * @throws \ReflectionException
     * @throws Exception
     */
    private function invokeCallback(callable $callback)
    {
        $reflection = new ReflectionFunction($callback);
        $params = $reflection->getParameters();

        $arguments = array_map(function ($param) {
            $type = $param->getType();
            if (!$type instanceof ReflectionNamedType)
                throw new Exception("Parameter type is required");

            $name = $param->getType()->getName();
            if (get_class($this->context->from) == $name)
                return $this->context->from;

            throw new Exception("Could not resolve source for parameter $name ($param->name)");
        }, $params);

        // invoke callback with arguments
        return $callback(...$arguments);
    }


    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    function where(callable $callback): Queryable
    {
        // inspect parameters and their types
        $ret = $this->invokeCallback($callback);
        $this->context->where[] = $ret;
        return $this;
    }

    function orderBy(callable $callback): Queryable
    {
        // inspect parameters and their types
        $ret = $this->invokeCallback($callback);
        $this->context->orderBy[] = $ret;
        return $this;
    }

    function fetchArray(): array
    {
        return [];
    }

    function toSql(): string
    {
        return $this->context->toSql();
    }
}