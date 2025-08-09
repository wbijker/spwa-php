<?php

namespace CodeQuery\Schema;


class SqlContext
{
    private array $cache = [];

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
