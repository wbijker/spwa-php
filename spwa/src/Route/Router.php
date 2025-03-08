<?php

namespace Spwa\Route;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;
use Spwa\Http\HttpRequestPath;
use Spwa\Js\Console;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;
use Spwa\Nodes\StateManager;


class Router extends Component
{
    private static ?string $uri = null;
    private static ?string $navigateUri = null;

    public static function navigate(string $uri): void
    {
        self::$navigateUri = $uri;
    }

    /**
     * @param Route[] $routes
     */
    public function __construct(private array $routes, private $fallback = null)
    {

    }

    function initialPhase(): void
    {
        // previous location injected by JS
        // for clientside routing we need to know what the previous URL looked like
        self::$uri = getallheaders()['Url'] ?? $_SERVER['REQUEST_URI'];
    }
    function patchPhase(): void
    {
        self::$uri = self::$navigateUri ?? $_SERVER['REQUEST_URI'];
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
        $path = new HttpRequestPath(self::$uri);

        foreach ($this->routes as $route) {
            if (is_string($route->path)) {
                if ($route->path == $path->uri()) {
                    return $this->invokeOrGet($route->component, null);
                }
                continue;
            }

            if ($route->path instanceof RoutePath) {
                $match = $route->path->match($path);
                if ($match != null) {
                    // match is the route params
                    // bind params to the actual class
                    $binding = self::bind($route->path->class, $match);
                    return $this->invokeOrGet($route->component, $binding);
                }
            }
        }
        return $this->invokeOrGet($this->fallback, null);
    }

    private static function bind($class, $params): object
    {
        $instance = new $class;
        $reflection = new ReflectionClass($instance::class);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $type = $property->getType();
            $name = $property->getName();
            self::coarsePart($type, $instance->$name, $params[$name] ?? null);
        }
        return $instance;
    }

    private static function coarsePart(?ReflectionType $type, &$ref, $value): void
    {
        if ($type instanceof ReflectionNamedType) {

            if ($type->allowsNull() && $value === null) {
                $ref = null;
                return;
            }

            $ref = match ($type->getName()) {
                'integer' => intval($value),
                'float' => floatval($value),
                'double' => doubleval($value),
                'boolean' => boolval($value),
                'string' => (string)$value,
                'NULL' => null,
                default => $value,
            };
        }
    }


    function render(): Node
    {
        return $this->findRoute();
    }
}