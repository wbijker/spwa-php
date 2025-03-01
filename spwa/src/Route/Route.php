<?php

namespace Spwa\Route;

use Spwa\Http\HttpRequestPath;

class Route
{
    public function __construct(public string|RouteFormat $path, public $component)
    {
    }

    public function match(HttpRequestPath $path): bool|RouteFormat|null
    {
        if (class_exists($this->path)) {
            // call static method on this class
            return $this->path::parse($path->getSegments(), $path->queryParams());
        }
        if (is_string($this->path)) {
            return $this->path === $path->uri();
        }
        return null;
    }
}