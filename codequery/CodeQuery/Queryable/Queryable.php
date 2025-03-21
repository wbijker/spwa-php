<?php

namespace CodeQuery\Queryable;

use App\Components\Selection;
use CodeQuery\Columns\BoolColumn;
use CodeQuery\Columns\Column;
use CodeQuery\Columns\IntColumn;
use CodeQuery\Expressions\AliasExpression;
use CodeQuery\Expressions\ConstExpression;
use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Sources\SqlSource;
use Exception;
use ReflectionClass;
use ReflectionFunction;
use ReflectionNamedType;

function toExpression($value): SqlExpression
{
    if ($value instanceof Column)
        return $value->exp;

    if ($value instanceof SqlExpression)
        return $value;

    throw new Exception("Expecting SqlExpression or Column");
}

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
            $select[$key] = new AliasExpression(toExpression($value), $key);
        }

        $this->context->select = new SqlSelect($select, $ret);
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
            $instance = $this->context->from->getInstance();
            if ($instance::class == $name)
                return $instance;

            throw new Exception("Could not resolve source for parameter $name ($param->name)");
        }, $params);

        // invoke callback with arguments
        return $callback(...$arguments);
    }


    /**
     * @param callable(mixed ...$args): BoolColumn $callback
     * @return $this
     * @throws \ReflectionException
     * @throws \Exception
     */
    function where(callable $callback): Queryable
    {
        // inspect parameters and their types
        $ret = $this->invokeCallback($callback);
        $this->context->where[] = toExpression($ret);
        return $this;
    }

    /**
     * @param callable(mixed ...$args): Column $callback
     * @return $this
     * @throws \ReflectionException
     * @throws \Exception
     */
    function orderBy(callable $callback): Queryable
    {
        // inspect parameters and their types
        $ret = $this->invokeCallback($callback);
        $this->context->orderBy[] = toExpression($ret);
        return $this;
    }

    function fetchArray(): array
    {
        echo $this->toSql();
        // 4: Execute SQL: [ ['c1' => 1, 'c2' => 'P1', 'c3' => 12.12], ['c1' => 2, 'c2' => 'P2', 'c3' => 43.4] ]
        $result = [
            ['id' => 1, 'name' => 'Product#1', 'price2' => 12.12],
            ['id' => 2, 'name' => 'Product#2', 'price2' => 43.4],
            ['id' => 3, 'name' => 'Product#3', 'price2' => 54.2],
            ['id' => 4, 'name' => 'Product#4', 'price2' => 892.3],
        ];

        $ret = [];
        foreach ($result as $row) {
            $obj = $this->context->select->populateRow($row);
            $ret[] = $obj;
        }
        return $ret;
    }

    function toSql(): string
    {
        return $this->context->toSql();
    }

}