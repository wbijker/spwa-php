<?php

namespace App;

use App\Components\HomeComponent;
use Spwa\App;

require 'vendor/autoload.php';


App::render(new HomeComponent());