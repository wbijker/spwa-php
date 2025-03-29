<?php

namespace CodeQuery\Queryable;

use App\Components\Selection;
use CodeQuery\Expressions\BinaryExpression;
use CodeQuery\Expressions\ConstExpression;
use CodeQuery\Expressions\SqlExpression;
use CodeQuery\Sources\SqlSource;

class SqlContext
{
    public SqlSelect $select;

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
    public SqlRootContext $root;

    /**
     * @param SqlSource $source
     * @param SqlRootContext $root
     */
    function __construct(SqlSource $source, SqlRootContext $root)
    {
        $this->from = $source;
        $this->root = $root;
        $this->select = new SqlSelect([], null);
    }

    function subContext(SqlSource $source): SqlContext
    {
        return new SqlContext($source, $this->root);
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

        $columns = empty($this->select->columns)
            ? ["*"]
            : array_map(fn(SqlExpression $expr) => $expr->toSql(), $this->select->columns);

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
            $sql .= "WHERE" . $whereClause;
        }

        if (!empty($this->groupBy)) {
            $groupByClause = implode(",\n", array_map(fn(SqlExpression $expr) => $expr->toSql(), $this->groupBy));
            $sql .= "GROUP BY" . $groupByClause;
        }

        if (!empty($this->orderBy)) {
            $orderByClause = implode(",\n", array_map(fn(SqlExpression $expr) => $expr->toSql(), $this->orderBy));
            $sql .= "ORDER BY" . $orderByClause;
        }

        return trim($sql);
    }

}