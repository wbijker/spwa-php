<?php

namespace App\Components;

use Spwa\Route\RouteFormat;
use Spwa\Route\RoutePath;
use Spwa\Route\RouteQueryParam;
use Spwa\Route\RouteSegment;

#[RoutePath("products")]
class ProductRoute extends RouteFormat
{
    #[RouteSegment(0)]
    var string $category;
    #[RouteSegment(1)]
    var int $product;
    #[RouteQueryParam("limit")]
    var ?int $limit;

    /**
     * @param string $category
     * @param int $product
     * @param int|null $limit
     */
    public function __construct(string $category = "", int $product = 0, ?int $limit = null)
    {
        $this->category = $category;
        $this->product = $product;
        $this->limit = $limit;
        parent::__construct();
    }
}