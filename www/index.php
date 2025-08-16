<?php

namespace App;

use App\Components\WelcomePage;
use App\Db\Category;
use App\Db\Product;
use CodeQuery\Columns\IntColumn;
use CodeQuery\Queryable\Query;
use CodeQuery\Schema\SqlContext;
use Spwa\App;
use Spwa\SpwMiddleware;

require 'vendor/autoload.php';

class ProductAgg
{
    public function __construct(
        public IntColumn $categoryId,
        public IntColumn $count
    )
    {
    }
}

$query = Query::from(Product::class)
    ->where(fn(Product $p) => $p->price->greaterThan(0))
    ->groupBy(fn(Product $p) => $p->category_id)
    ->select(fn(Product $p) => new ProductAgg(
        categoryId: $p->category_id->add(100),
        count: $p->id->count()->multiply(2)
    ))
    ->innerJoin(Category::class, fn(ProductAgg $agg, Category $cat) => $agg->categoryId->equals($cat->id))
    ->select(fn(ProductAgg $agg, Category $cat) => (object)[
        'categoryId' => $agg->categoryId,
        'count' => $agg->count->multiply(3),
        'name' => $cat->name,
    ]);

//    ->innerJoin(fn(ProductAgg $agg, Category $cat) => $agg->categoryId->equals($cat->id))
//    ->select(fn(ProductAgg $agg, Category $cat) => [
//        'categoryName' => $cat->name,
//        'categoryId' => $cat->id,
//        'count' => $agg->count
//    ]);


//    ->select(fn(Product $product) => (object)[
//        'id' => $product->id,
//        'name' => $product->name,
//        'categoryName' => $product->category()->name,
//        'categoryId' => $product->category()->id,
//        'price' => $product->price->multiply(3.14),
//        'isAbove10' => $product->category_id->greaterOrEqual(10),
//    ]);

echo $query->toSql();



//App::run([
//    new SpwMiddleware(fn() => new WelcomePage()),
//]);


