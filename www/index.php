<?php

namespace App;

use App\Components\WelcomePage;
use App\Db\Product;
use CodeQuery\Queryable\Query;
use CodeQuery\Schema\SqlContext;
use Spwa\App;
use Spwa\SpwMiddleware;

require 'vendor/autoload.php';


$query = Query::from(Product::class);

echo "done deal";


//App::run([
//    new SpwMiddleware(fn() => new WelcomePage()),
//]);


