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

function toExpression(SqlExpression|Column $value): SqlExpression
{
    if ($value instanceof Column)
        return $value->exp;

    return $value;
}

class Query
{
    public function __construct(private SqlContext $context)
    {
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
        if (!$order instanceof Column) {
            throw new \InvalidArgumentException("Order by must return an instance of " . Column::class . ", got " . gettype($order));
        }
        $this->context->orderBy[] = new SortExpression($order->exp, $direction);
        return $this;
    }

    function orderByDesc(callable $callback): self
    {
        return $this->orderBy($callback, self::ORDER_DESC);
    }

    function select(callable $callback): self
    {
        if (!empty($this->context->select))
        {
            $context = new SqlContext();
            $context->from = new QuerySource($this->context);
            $context->from->setAlias($context->nextAlias());

            $last = $this->context->selectType;
            // loop through all properties of $last
            foreach ((array)$last as $key => $value) {
                if (!$value instanceof Column) {
                    throw new \InvalidArgumentException("Selection property '$key' must be an instance of " . Column::class . ", got " . gettype($value));
                }
                $last->$key = $value->createAlias(new ColumnExpression($key, $context->from));
            }

            $context->sources[get_class($this->context->selectType)] = $last;

            $q = new Query($context);
            $q->select($callback);
            return $q;
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

    /** @param class-string $class */
    function fetch(string $class): array
    {
        echo "SQL to execute" . $this->toSql();
        return [];

        $result = [
            ['id' => 1, 'name' => 'Product#1', 'price' => 12.12],
            ['id' => 2, 'name' => 'Product#2', 'price' => 43.4],
            ['id' => 3, 'name' => 'Product#3', 'price' => 54.2],
            ['id' => 4, 'name' => 'Product#4', 'price' => 892.3],
        ];

        $ret = [];
        foreach ($result as $row) {
            /** @var object $obj */
            $obj = new $class();
            $this->context->select->populateRow($row, $obj);
            $ret[] = $obj;
        }
        print_r($ret);

        return $ret;
    }

    function toSql(): string
    {
        return $this->context->toSql();
    }

}