<?php

namespace Spwa\Route;

use Spwa\Http\HttpRequestPath;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;

class Router extends Component
{
    private static ?string $uri = null;

    public static function navigate(string $uri): void
    {
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
            if (is_string($route->path)) {
                if ($route->path == $path->uri()) {
                    return $this->invokeOrGet($route->component, null);
                }
                continue;
            }

            if ($route->path instanceof RoutePath) {
                $instance = $route->path->match($path);
                if ($instance != null) {

                }
            }
        }
        return $this->invokeOrGet($this->fallback, null);
    }

    function render(): Node
    {
        return $this->findRoute();
    }
}