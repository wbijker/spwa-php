<?php

namespace Spwa\Route;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class RouteQueryParam
{
    public function __construct(public string $name)
    {
    }
}