<?php

namespace CodeQuery\Schema;


class SqlContext
{
    private array $cache = [];

    public function tableInstance(string $tableClass): Table
    {
        $hit = $this->cache[$tableClass] ?? null;
        if ($hit) {
            return $hit;
        }

        $table = new $tableClass($this);
        if (!$table instanceof Table) {
            throw new \InvalidArgumentException("Class $tableClass must be an instance of " . Table::class);
        }
        // invoke the builder to build the table structure
        $table->buildTable(new TableBuilder($table));
        $this->cache[$tableClass] = $table;
        return $table;
    }

}
