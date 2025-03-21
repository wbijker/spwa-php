<?php

namespace CodeQuery\Schema;

abstract class Table
{
    abstract protected function build(TableBuilder $builder): void;

    abstract protected function tableName(): string;
}