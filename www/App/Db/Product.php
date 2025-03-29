<?php

namespace App\Db;

use CodeQuery\Columns\FloatColumn;
use CodeQuery\Columns\IntColumn;
use CodeQuery\Columns\StringColumn;
use CodeQuery\Schema\Table;

class Product extends Table
{
    public IntColumn $id;
    public StringColumn $name;
    public IntColumn $category_id;
    public FloatColumn $price;

    public function __construct()
    {
        parent::__construct("product");
        
        $this->id = $this->int("id")
            ->primaryKey()
            ->autoIncrement();

        $this->name = $this->string("name");

        $this->category_id = $this
            ->int("category_id")
            ->foreignKey(fn(Category $c) => $c->id);

        $this->price = $this->float("price");

    }
}
