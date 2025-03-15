<?php

namespace CodeQuery\Queryable;

use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Sources\TableSource;
use mysql_xdevapi\Exception;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;

class Queryable
{

    private ?SqlContext $context = null;

    function select(callable $callback): Queryable
    {
        return $this;
    }

    /**
     * @param ReflectionParameter[] $reflection
     * @return void
     */
    private function fillContextIfNull(array $params): void
    {
        if ($this->context != null)
            return;

        if (count($params) != 1)
            throw new Exception("Only one parameter is allowed");

        $param = $params[0];
        $type = $param->getType();
        if (!$type instanceof ReflectionNamedType)
            throw new Exception("Parameter type is required");

//        if (is_subclass_of($type->getName(), TableSource::class))

        $this->context = new SqlContext(new TableSource($type->getName()), new SqlRootContext());
    }


    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    function where(callable $callback): Queryable
    {
        // inspect parameters and their types
        $reflection = new ReflectionFunction($callback);
        $params = $reflection->getParameters();
        $this->fillContextIfNull($params);


        foreach ($params as $param) {
            $type = $param->getType();
            if (!$type instanceof ReflectionNamedType) {
                throw new \Exception("Parameter type is required");
            }

            // check context sources
            if ($this->context == null)

                echo "Parameter: " . $param->getName() . "\n";
            echo "  Type: " . $type->getName() . "\n";
        }
        return $this;
    }

    function fetchArray(): array
    {
        return [];
    }
}