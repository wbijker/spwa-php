<?php

namespace CodeQuery;

class DbInt extends DbColumn
{
    public function eq(int $int): DbBool
    {
        return new DbBool();
    }
}