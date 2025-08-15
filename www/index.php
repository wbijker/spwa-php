<?php

namespace App;

use App\Components\WelcomePage;
use App\Db\Product;
use CodeQuery\Queryable\Query;
use CodeQuery\Schema\SqlContext;
use Spwa\App;
use Spwa\SpwMiddleware;

require 'vendor/autoload.php';


// created SQl context. Holding sources, select, where, group by, order by, joins
// each block defined it's own context.

$query = Query::from(Product::class)
    ->select(fn(Product $product) => (object)[
        'id' => $product->id,
        'name' => $product->name,
        'categoryName' => $product->category()->name,
        'categoryId' => $product->category()->id,
        'price' => $product->price->multiply(3.14),
        'isAbove10' => $product->category_id->greaterOrEqual(10),
    ]);

echo $query->toSql();



//App::run([
//    new SpwMiddleware(fn() => new WelcomePage()),
//]);


