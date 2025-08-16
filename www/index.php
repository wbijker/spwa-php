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
    public IntColumn $categoryId,
    public IntColumn $count,
    public IntColumn $row,
    public StringColumn $name
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


/* @var ProductAgg[] $results  */
$results = $query->fetch();
foreach ($results as $agg) {
    echo "Category ID: " . $agg->categoryId->value . "\n";
    echo "Count: " . $agg->count->value . "\n";
    echo "Row: " . $agg->row->value . "\n";
    echo "Name: " . $agg->name->value . "\n";
    echo "-------------------\n";
}

//$a = new ProductAgg();
//echo $a->name->value;
//echo $a->categoryId->value;
//echo $a->count->value;

//    ->fetch(fn(ProductAgg $agg) => new ProductAggFetch());

//    ->innerJoin(Category::class, fn(ProductAgg $agg, Category $cat) => $agg->categoryId->equals($cat->id))
//    ->select(fn(ProductAgg $agg, Category $cat) => (object)[
//        'categoryId' => $agg->categoryId,
//        'count' => $agg->count->multiply(3),
//        'name' => $cat->name,
//    ]);




//App::run([
//    new SpwMiddleware(fn() => new WelcomePage()),
//]);


