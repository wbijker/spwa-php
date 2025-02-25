<?php

namespace Spwa\Route;

use Spwa\Http\HttpRequest;
use Spwa\Js\JS;

class Route
{
    public function __construct(public string|RouteFormat $path, public $component)
    {
    }

    public function match(HttpRequest $request): bool|RouteFormat|null
    {
        if (class_exists($this->path)) {
            // call static method on this class
            return $this->path::parse($request->segments(), $request->queryParams());
        }
        if (is_string($this->path)) {
            return $this->path === $request->uri();
        }
        return null;
    }
}