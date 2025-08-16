<?php

namespace App;

use App\Components\WelcomePage;
use App\Db\Category;
use App\Db\Product;
use CodeQuery\Columns\IntColumn;
use CodeQuery\Columns\StringColumn;
use CodeQuery\Expressions\Frame;
use CodeQuery\Queryable\Query;
use CodeQuery\Queryable\WindowFunctions;
use CodeQuery\Schema\SqlContext;
use ReflectionFunction;
use Spwa\App;
use Spwa\SpwMiddleware;

require 'vendor/autoload.php';

class ProductAgg
{
    public function __construct(
        public IntColumn    $categoryId,
        public IntColumn    $count,
        public IntColumn    $row,
        public StringColumn $name
    )
    {
    }

    public function toFlat(): ProductAggFlat
    {
        return new ProductAggFlat(
            categoryId: $this->categoryId->value,
            count: $this->count->value,
            row: $this->row->value,
            name: $this->name->value
        );
    }
}

class ProductAggFlat
{
    public function __construct(
        public int    $categoryId,
        public int    $count,
        public int    $row,
        public string $name
    )
    {
    }

}

/*SELECT
    t0.categoryId AS categoryId,
    t0.count * 3 AS count,
    t1.name AS name
FROM
(
    SELECT
            t0.category_id + 100 AS categoryId,
            count(t0.id) * 2 AS count,
            t0.name AS name
        FROM
            `product` t0
        WHERE
            t0.price > 0
        GROUP BY
            t0.category_id
    ) t0
    INNER JOIN `category` t1 ON t0.categoryId = t1.id*/

// create database File containing all associated tables
// migrations dumping an diffing the database schema
// generate file containing the above;

// SqlDriver + sql generator;

$query = Query::from(Product::class)
    ->where(fn(Product $p) => $p->price->greaterThan(0))
    ->select(fn(Product $p) => new ProductAgg(
        categoryId: $p->category_id->add(100),
        count: $p->id->count()->multiply(2),
        row: WindowFunctions::rowNumber($p->id, null, Frame::rows(null, null)),
        name: $p->category()->name
    ));


/* @var ProductAggFlat[] $results  */
$results = $query->fetch(fn(ProductAgg $agg) => $agg->toFlat());

print_r($results);



//App::run([
//    new SpwMiddleware(fn() => new WelcomePage()),
//]);


