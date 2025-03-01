<?php

namespace Spwa\Route;

use Spwa\Http\HttpRequest;
use Spwa\Http\HttpRequestPath;
use Spwa\Js\Console;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;

class Router extends Component
{
    private static ?string $uri = null;

    public static function navigate(string $uri): void {
        self::$uri = $uri;
    }

    /**
     * @param Route[] $routes
     */
    public function __construct(private array $routes, private $fallback = null)
    {

    }

    private function invokeOrGet($component, $param): Node
    {
        if ($component instanceof Component) {
            return $component;
        }
        if (is_callable($component)) {
            return ($component)($param);
        }
        return new HtmlText("404 Not Found");
    }


    private function findRoute(): Node
    {
        $uri = self::$uri ?? $_SERVER['REQUEST_URI'];
        $path = new HttpRequestPath($uri);

        foreach ($this->routes as $route) {

            $found = $route->match($path);
            if ($found) {
                return $this->invokeOrGet($route->component, $found);
            }
        }
        return $this->invokeOrGet($this->fallback, null);
    }

    function render(): Node
    {
        return $this->findRoute();
    }
}