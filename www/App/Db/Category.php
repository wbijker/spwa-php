<?php

namespace App\Db;

use CodeQuery\Columns\IntColumn;
use CodeQuery\Columns\StringColumn;
use CodeQuery\Schema\Table;

class Category extends Table
{
    public IntColumn $id;
    public StringColumn $name;

    public function __construct()
    {
        parent::__construct('category');
        
        $this->id = $this->int('id')->primaryKey()->autoIncrement();
        $this->name = $this->string('name');
    }
}