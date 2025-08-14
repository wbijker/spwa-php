<?php

namespace CodeQuery\Queryable;

use CodeQuery\Columns\BoolColumn;
use CodeQuery\Columns\Column;
use CodeQuery\Expressions\AliasExpression;
use CodeQuery\Expressions\ColumnExpression;
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

    function where(BoolColumn $predicate): self
    {
        $this->context->where[] = $predicate->exp;
        return $this;
    }

    function groupBy(Column $column): self
    {
        $this->context->groupBy[] = $column->exp;
        return $this;
    }

    function orderBy(Column $column): self
    {
        $this->context->orderBy[] = $column->exp;
        return $this;
    }

    function select(callable $callback): self
    {
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
            $select[$key] = $value->exp;
        }

        $this->context->select = $select;
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