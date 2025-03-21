<?php

namespace CodeQuery\Queryable;

class SqlRootContext
{
    private array $prefixes = [];

    function alias(string $prefix): string
    {
        if (!isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = 1;
            return $prefix;
        }

        $this->prefixes[$prefix]++;
        return $prefix . $this->prefixes[$prefix];
    }
}