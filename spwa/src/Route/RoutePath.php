<?php

namespace Spwa\Route;


use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class RoutePath {
    public function __construct(public string $path) {}
}
