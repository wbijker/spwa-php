<?php

namespace App;

use App\Components\WelcomePage;
use App\Db\Category;
use App\Db\Product;
use CodeQuery\Columns\IntColumn;
use CodeQuery\Columns\StringColumn;
use CodeQuery\Expressions\Frame;
use CodeQuery\Queryable\Database;
use CodeQuery\Queryable\Query;
use CodeQuery\Queryable\WindowFunction;
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

// scoped create all table sources

// Select category_id, max(id) from product groupby category_id order by id desc
// join back to products to get name


/*
The from and join clauses is the only place where a new source can be introduced.

$source = table | query | constant | function

Query::from($source);
->join($source);


Scoped is just the grouping of sources for a query.
*/

$q = Database::scoped(fn(Product $p, Category $c, Query $q) => $q
    ->from($p)
    ->select(new ProductAgg(
        categoryId: $p->category_id->add(100),
        count: $p->id->count()->multiply(2),
        row: WindowFunction::rowNumber($p->id),
        name: StringColumn::case()
            ->when($p->category_id->equals(0), $c->name)
            ->when($p->category_id->equals(1), "Other")
            ->end()
    ))
);

echo $q->toSql();



//Query::scoped(fn(Product $p) => Query::from($p)
//    ->groupBy($p->category_id)
//    ->orderByDesc($p->id)
//    ->select(new AA(
//            id: $p->id->max(),
//            categoryId: $p->category_id,
//        )
//    ))
//    ->scoped(fn(AA $a, Product $p) => Query::from($p)
//        ->innerJoin($a, $a->id->equals($p->id))
//        // laterJoin / crossLateralJoin
//        ->select(new ProductSelector(
//            id: $p->id,
//            categoryId: $p->category_id,
//            name: $p->name
//        ))
//    );
//
//// -> innerJoin(Newtable::class, fn(Newtable $n, Product $p) => $n->id->equals($p->id))
//// -> lateralJoin(fn(Sources $s) => ...);
//
//$q = Query::scoped(fn(Product $p, Category $c) => Query::from($p)
//    ->innerJoin($c, $c->id->equals($p->category_id))
//    ->where($p->price->greaterThan(10))
//    ->orderByDesc($p->price)
//    ->orderBy($p->category_id)
//    ->select(new ProductAgg(
//        categoryId: $p->category_id->add(100),
//        count: $p->id->count()->multiply(2),
//        row: WindowFunction::rowNumber($p->id),
//        name: StringColumn::case()
//            ->when($p->category_id->equals(0), $c->name)
//            ->when($p->category_id->equals(1), "Other")
//            ->end()
//    ))
//    ->scoped(fn(ProductAgg $p, Product $p) =>
//        )
//);
//

//App::run([
//    new SpwMiddleware(fn() => new WelcomePage()),
//]);


