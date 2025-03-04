<?php

namespace Spwa\Route;

class Route
{
    public function __construct(public RoutePath|string $path, public $component)
    {
    }

}