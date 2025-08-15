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
    ) {}
}

$query = Query::from(Product::class)
    ->groupBy(fn(Product $p) => $p->category_id)
    ->select(fn(Product $p) => new ProductAgg(
        categoryId: $p->category_id,
        count: $p->id->count()
    ));
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


