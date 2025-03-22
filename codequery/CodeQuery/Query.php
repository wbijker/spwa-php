<?php

namespace CodeQuery;

use CodeQuery\Columns\BoolColumn;
use CodeQuery\Columns\Column;
use CodeQuery\Expressions\AliasExpression;
use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Queryable\SqlContext;
use CodeQuery\Queryable\SqlRootContext;
use CodeQuery\Queryable\SqlSelect;
use CodeQuery\Schema\Table;
use CodeQuery\Sources\SqlSource;
use CodeQuery\Sources\TableSource;
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

    function orderBy(Column $column): self
    {
        $this->context->orderBy[] = $column->exp;
        return $this;
    }

    function select(mixed $selection): self
    {
        // 1: Trace all members. Inspect members of Selection: ($p->id, $p->name, $p->price->multiply(2))
        $vars = get_object_vars($selection);

        // 2: Alias members per selection. Select p.id as c1, p.name as c2, p.price * 2 as c3
        $select = [];
        foreach ($vars as $key => $value) {
            $select[$key] = new AliasExpression(toExpression($value), $key);
        }

        $this->context->select = new SqlSelect($select, $selection);
        return $this;
    }

    function fetchArray(): array
    {
        echo $this->toSql();
        return [];
    }

    function toSql(): string
    {
        return $this->context->toSql();
    }

}