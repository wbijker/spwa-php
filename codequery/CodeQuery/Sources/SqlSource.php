<?php

namespace CodeQuery\Sources;

abstract class SqlSource
{
    abstract function toSql(): string;

    abstract function getInstance();
}

