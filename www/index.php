<?php

namespace App;

use App\Components\WelcomePage;
use Spwa\App;
use Spwa\SpwMiddleware;

require 'vendor/autoload.php';


App::run([
    new SpwMiddleware(new WelcomePage()),
]);


