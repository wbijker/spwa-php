<?php

use Spwa\App;
use App\Components\Welcome;

require 'vendor/autoload.php';

echo "Very good";

App::render(new Welcome());
