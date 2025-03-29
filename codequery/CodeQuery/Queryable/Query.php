<?php

namespace CodeQuery\Queryable;

use CodeQuery\Columns\BoolColumn;
use CodeQuery\Columns\Column;
use CodeQuery\Expressions\AliasExpression;
use CodeQuery\Expressions\ColumnExpression;
use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Schema\Table;
use CodeQuery\Sources\QuerySource;
use CodeQuery\Sources\SqlSource;
use Exception;

function toExpression($value): SqlExpression
{
    if ($value instanceof Column)
        return $value->exp;

    if ($value instanceof SqlExpression)
        return $value;

    throw new Exception("Expecting SqlExpression or Column");
}

class Query
{
    private SqlContext $context;

    public function __construct(SqlSource $source, SqlRootContext $root)
    {
        $this->context = new SqlContext($source, $root);
    }

    static function from(Table $table): self
    {
        $root = new SqlRootContext();
        $source = $table->getSource();
        $source->setAlias($root);
        return new Query($source, $root);
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

    function select(array|object $selection): self
    {
        $vars = is_array($selection)
            ? $selection
            : get_object_vars($selection);

        // alias members per selection. Select p.id as c1, p.name as c2, p.price * 2 as c3
        $select = [];
        foreach ($vars as $key => $value) {
            $select[$key] = new AliasExpression(toExpression($value), $key);
        }

        // create sub subQuery
        if ($this->context->select->instance != null) {

            $query = new QuerySource($this->context);
            $flat = $this->context->select->instance;
            foreach ($flat as $key => $value) {
                $flat->$key = new AliasExpression(new ColumnExpression($key, $query), $key);
            }

            $query->setAlias($this->context->root);
            $this->context = $this->context->subContext($query);
        }

        $this->context->select = new SqlSelect($select, $selection);


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