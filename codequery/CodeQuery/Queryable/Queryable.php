<?php

namespace CodeQuery\Queryable;

use CodeQuery\Columns\Column;
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
     */
    function select(callable $callback): Queryable
    {
        $ret = $this->invokeCallback($callback);
        $vars = get_object_vars($ret);
        $select = [];

        foreach ($vars as $key => $value) {
            if ($value instanceof SqlExpression) {
                $select[] = $value;
            }
        }
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