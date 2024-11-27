<?php

namespace App;

use App\Components\WelcomePage;
use Spwa\App;

require 'vendor/autoload.php';


App::render(new WelcomePage());