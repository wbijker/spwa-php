<?php

namespace CodeQuery\Queryable;

use CodeQuery\Columns\BoolColumn;
use CodeQuery\Columns\Column;
use CodeQuery\Expressions\AliasExpression;
use CodeQuery\Expressions\ColumnExpression;
use CodeQuery\Expressions\SortExpression;
use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Schema\SqlContext;
use CodeQuery\Sources\QuerySource;
use CodeQuery\Sources\SqlSource;

class Query
{
    public function __construct(private SqlContext $context)
    {
    }

    static function toExpression(SqlExpression|Column $value): SqlExpression
    {
        if ($value instanceof Column)
            return $value->exp;

        return $value;
    }

    static function from(string $className): self
    {
        $context = new SqlContext();
        $builder = $context->build($className);
        $context->from = $builder->source;
        return new Query($context);
    }

    // BoolColumn $predicate
    function where(callable $callback): self
    {
        $condition = $this->context->invokeCallback($callback);
        if (!$condition instanceof BoolColumn) {
            throw new \InvalidArgumentException("Where condition must return an instance of " . BoolColumn::class . ", got " . gettype($condition));
        }
        $this->context->where[] = $condition->exp;
        return $this;
    }

    function groupBy(callable $callback): self
    {
        $group = $this->context->invokeCallback($callback);
        // or array
        if (!$group instanceof Column) {
            throw new \InvalidArgumentException("Group by must return an instance of " . Column::class . ", got " . gettype($group));
        }
        $this->context->groupBy = [$group->exp];
        return $this;
    }

    const ORDER_ASC = 0;
    const ORDER_DESC = 1;

    function orderBy(callable $callback, int $direction = self::ORDER_ASC): self
    {
        $order = $this->context->invokeCallback($callback);
        // or array
        if (!$order instanceof Column && !$order instanceof SqlExpression) {
            throw new \InvalidArgumentException("Order by must return an instance of " . Column::class . ", got " . gettype($order));
        }
        $this->context->orderBy[] = new SortExpression(self::toExpression($order), $direction);
        return $this;
    }

    function orderByDesc(callable $callback): self
    {
        return $this->orderBy($callback, self::ORDER_DESC);
    }

    function innerJoin(string|SqlSource $source, callable $callback): self
    {
        // innerJoin just after a select will create a subquery
        if (!empty($this->context->select)) {
            $q = $this->context->createSubQuery();
            return $q->innerJoin($source, $callback);
        }

        if ($source instanceof SqlSource) {
            throw new \Exception("Not implemented yet: join with SqlSource");
        }

        $builder = $this->context->build($source);
        $instance = $builder->source->instance;
        $this->context->sources[$source] = $instance;

        $condition = $this->context->invokeCallback($callback);
        $this->context->joins[] = new SqlJoin(
            "inner",
            $builder->source,
            self::toExpression($condition)
        );
        return $this;
    }

    function select(callable $callback): self
    {
        if (!empty($this->context->select)) {
            $q = $this->context->createSubQuery();
            return $q->select($callback);
        }

        $selection = $this->context->invokeCallback($callback);
        if (gettype($selection) != 'object') {
            throw new \InvalidArgumentException("Selection must return an object, got " . gettype($selection));
        }

        // convert object to array
        // make sure each property is a SqlExpression
        // and select the underlying expression
        $select = [];
        foreach ((array)$selection as $key => $value) {
            if (!$value instanceof Column) {
                throw new \InvalidArgumentException("Selection property '$key' must be an instance of " . Column::class . ", got " . gettype($value));
            }
            $select[$key] = new AliasExpression($value->exp, $key);
        }

        $this->context->select = $select;
        $this->context->selectType = $selection;
        return $this;
    }

    function fetch(): array
    {
        echo $this->toSql() . "\n";

        // $results is a mock of the database results
        $results = [
            [
                'categoryId' => 1,
                'count' => 1,
                'row' => 1,
                'name' => 'Product 1',
            ],
            [
                'categoryId' => 2,
                'count' => 22,
                'row' => 2,
                'name' => 'Product 2',
            ]
        ];

        return array_map(function ($row) {
            $instance = clone $this->context->selectType;
            foreach ($row as $key => $value) {
                if (property_exists($instance, $key)) {
                    $prop = clone $instance->$key;
                    $instance->$key = $prop;
                    if ($prop instanceof Column)
                        $prop->convertFrom($value);
                }
            }
            return $instance;
        }, $results);
    }

    function toSql(): string
    {
        return $this->context->toSql();
    }

}