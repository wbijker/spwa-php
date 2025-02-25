<?php

namespace Spwa\Route;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class RouteSegment
{
    public function __construct(public int $position)
    {
    }
}