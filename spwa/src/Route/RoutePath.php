<?php

namespace Spwa\Route;

use Spwa\Http\HttpRequestPath;
use Spwa\Js\Console;

/*
 * @template T extends RouteParams
 */

class RoutePath
{
    /*
     * @param string $path
     * @param class-string<T> $class
     */
    public function __construct(public string $path, public $class)
    {
    }

    /*
     * @param T $instance
     */
    public function toUrl($instance): string
    {
        return "";
    }

    public function match(HttpRequestPath $path): ?array
    {
        Console::log("Trying to match " . $this->path . " with " . $path->uri());
        return null;
    }

}