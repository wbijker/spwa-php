<?php

namespace CodeQuery\Queryable;

use CodeQuery\Columns\BoolColumn;
use CodeQuery\Columns\Column;
use CodeQuery\Columns\IntColumn;
use CodeQuery\Expressions\AliasExpression;
use CodeQuery\Expressions\BinaryExpression;
use CodeQuery\Expressions\ColumnExpression;
use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Schema\Table;
use CodeQuery\Sources\QuerySource;
use CodeQuery\Sources\SqlSource;
use Exception;
use mysql_xdevapi\SqlStatement;

function toExpression(SqlExpression|Column $value): SqlExpression
{
    if ($value instanceof Column)
        return $value->exp;

    return $value;
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

    function select(object $selection): self
    {
        $nextSource = new QuerySource($this->context);
        $next = [];
        $select = [];
        // alias members per selection. Select p.id as c1, p.name as c2, p.price * 2 as c3
        foreach ($selection as $key => $value) {
            $exp = toExpression($value);
            $select[] = new AliasExpression($exp, $key);
            $n = new ColumnExpression($key, $nextSource);
            $selection->$key = $value->createAlias($n);
            $next[] = $n;
        }

        if ($this->context->select == null) {
            $this->context->select = new SqlSelect($select, null);
            $sql = $this->toSql();
        } else {

        }

        $this->context->next = new SqlContext($nextSource, $this->context->root);
        $this->context->next->select = new SqlSelect($next, null);

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