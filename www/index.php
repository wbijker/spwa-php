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

$query = Query::from(Product::class);

echo $query->toSql();



//App::run([
//    new SpwMiddleware(fn() => new WelcomePage()),
//]);


