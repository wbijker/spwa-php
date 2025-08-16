<?php

namespace CodeQuery\Schema;


use CodeQuery\Columns\Column;
use CodeQuery\Expressions\BinaryExpression;
use CodeQuery\Expressions\ColumnExpression;
use CodeQuery\Expressions\ConstExpression;
use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Queryable\Query;
use CodeQuery\Queryable\SqlJoin;
use CodeQuery\Queryable\SqlSelect;
use CodeQuery\Sources\QuerySource;
use CodeQuery\Sources\SqlSource;
use CodeQuery\Sources\TableSource;

// created SQl context. Holding sources, select, where, group by, order by, joins
// each block defined it's own context.
// The scope of a SQL query
// Each table is created from a SqlContext

class SqlContext
{
    // combination of all sources used in this query
    // dictionary<type, SqlSource>
    // from + joins
    public array $sources = [];

//    private array $prefixes = [];
//
//    function alias(string $prefix): string
//    {
//        if (!isset($this->prefixes[$prefix])) {
//            $this->prefixes[$prefix] = 1;
//            return $prefix;
//        }
//
//        $this->prefixes[$prefix]++;
//        return $prefix . $this->prefixes[$prefix];
//    }

    /**
     * @var SqlExpression[] $select
     */
    public array $select = [];

    public ?object $selectType = null;

    /**
     * @var SqlExpression[] $where
     */
    public array $where = [];
    /**
     * @var SqlExpression[] $groupBy
     */
    public array $groupBy = [];
    /**
     * @var SqlExpression[] $orderBy
     */
    public array $orderBy = [];

    /**
     * @var SqlJoin[] $joins
     */
    public array $joins = [];

    public SqlSource $from;


    private int $aliasCounter = 0;

    public function nextAlias(): string
    {
        return "t" . $this->aliasCounter++;
    }

    public function build(string $tableClass): TableBuilder
    {
        $hit = $this->sources[$tableClass] ?? null;
        if ($hit) {
            return $hit;
        }

        $table = new $tableClass($this);
        if (!$table instanceof Table) {
            throw new \InvalidArgumentException("Class $tableClass must be an instance of " . Table::class);
        }
        $builder = new TableBuilder($table);
        // invoke the builder to build the table structure
        $table->buildTable($builder);
        $builder->source->setAlias($this->nextAlias());
        $this->sources[$tableClass] = $builder->table;

        return $builder;
    }


    public function createSubQuery(): Query
    {
        $context = new SqlContext();
        $context->from = new QuerySource($this);
        $context->from->setAlias($context->nextAlias());

        $last = $this->selectType;
        // loop through all properties of $last
        foreach ((array)$last as $key => $value) {
            if (!$value instanceof Column) {
                throw new \InvalidArgumentException("Selection property '$key' must be an instance of " . Column::class . ", got " . gettype($value));
            }
            $last->$key = $value->createAlias(new ColumnExpression($key, $context->from));
        }

        $context->sources[get_class($this->selectType)] = $last;
        return new Query($context);
    }

    public function invokeCallback(callable $callback, ?Query $query = null): mixed
    {
        // using reflection to get the parameters of the callback
        $reflection = new \ReflectionFunction($callback);
        $params = $reflection->getParameters();

        $filledParams = array_map(function ($param) use ($query) {
            if ($param->getType()->getName() == Query::class) {
                return $query;
            }

            $class = $param->getType()->getName();
            $hit = $this->sources[$class] ?? null;
            if ($hit == null) {
                throw new \InvalidArgumentException("Source $class not part of the query.");
            }
            return $hit;
        }, $params);

        // invoke the callback with the filled parameters
        $res = $callback(...$filledParams);
        return $res;
    }

    private function reduceWhere(array $where): SqlExpression
    {
        if (empty($where)) {
            return new ConstExpression(true);
        }

        $first = $where[0];

        return array_reduce(
            array_slice($where, 1),
            fn(SqlExpression $prev, SqlExpression $current) => new BinaryExpression($prev, "AND", $current),
            $first
        );
    }

    function toSql(): string
    {
        $sql = "SELECT\n";

        $columns = empty($this->select)
            ? ["*"]
            : array_map(fn(SqlExpression $expr) => $expr->toSql(), $this->select);

        $sql .= implode(",\n", $columns) . "\n";

        $sql .= "FROM\n";
        $sql .= $this->from->toSql() . "\n";

        if (!empty($this->joins)) {
            foreach ($this->joins as $join) {
                $sql .= strtoupper($join->type) . " JOIN ";
                $sql .= $join->source->toSql();
                $sql .= " ON " . $join->on->toSql() . "\n";
            }
        }

        if (!empty($this->where)) {
            $whereClause = $this->reduceWhere($this->where)->toSql();
            $sql .= " WHERE " . $whereClause;
        }

        if (!empty($this->groupBy)) {
            $groupByClause = implode(",\n", array_map(fn(SqlExpression $expr) => $expr->toSql(), $this->groupBy));
            $sql .= " GROUP BY " . $groupByClause;
        }

        if (!empty($this->orderBy)) {
            $orderByClause = implode(",\n", array_map(fn(SqlExpression $expr) => $expr->toSql(), $this->orderBy));
            $sql .= " ORDER BY " . $orderByClause;
        }

        return trim($sql);
    }


}
