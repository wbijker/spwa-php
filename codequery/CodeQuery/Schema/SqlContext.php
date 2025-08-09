<?php

namespace CodeQuery\Schema;


class SqlContext
{
    private array $cache = [];

    // aliases inside a subquery are local to that subquery
    // they are scoped to the block they're defined in
    function nextAlias(): string {
        return "";
    }

    public function build(string $tableClass): TableBuilder
    {
        $hit = $this->cache[$tableClass] ?? null;
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
        $this->cache[$tableClass] = $builder;

        return $builder;
    }

}
