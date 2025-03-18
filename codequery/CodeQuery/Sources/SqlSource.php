<?php

namespace CodeQuery\Sources;

abstract class SqlSource
{
    abstract function toSql(): string;
}

