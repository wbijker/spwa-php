<?php

namespace App\Components;

use Spwa\Route\RoutePath;

class Routes
{
    public static string $about = "/about";
    public static RoutePath $product;

    public static function init(): void
    {
        self::$product = new RoutePath("/products/{category}/{product}", ProductRoute::class);
    }
}

Routes::init();