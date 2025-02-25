<?php

namespace Spwa\Route;

use ReflectionClass;


abstract class RouteFormat
{
    protected string $basePath;
    protected array $segments = [];
    protected array $queryParams = [];

    protected static function initialize(?string $type): mixed
    {
        return match ($type) {
            'int' => 0,
            'float' => 0.0,
            'bool' => false,
            'string' => '',
            default => null,
        };
    }

    public function __construct()
    {
        $reflection = new ReflectionClass($this);
        $routeAttr = $reflection->getAttributes(RoutePath::class)[0] ?? null;

        $this->basePath = $routeAttr->newInstance()->path;

        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                $name = $property->getName();

                // initialize property if not initialized
                if (!isset($this->$name)) {
                    $type = $property->getType()?->getName();
                    $this->$name = self::initialize($type);
                }

                if ($instance instanceof RouteSegment) {
                    $this->segments[$instance->position] = &$this->$name;
                } elseif ($instance instanceof RouteQueryParam) {
                    $this->queryParams[$instance->name] = &$this->$name;
                }
            }
        }

        ksort($this->segments);
    }

    private static function coarseProperty(string $gettype, mixed $value)
    {
        return match ($gettype) {
            'integer' => intval($value),
            'float' => floatval($value),
            'double' => doubleval($value),
            'boolean' => boolval($value),
            'string' => (string)$value,
            'NULL' => null,
            default => $value,
        };
    }

    function toUrl(): string
    {
        $segments = array_map(fn($seg) => urlencode($seg ?? ''), $this->segments);
        $url = "/" . trim($this->basePath, '/') . '/' . implode('/', $segments);
        $queryParams = array_filter($this->queryParams, fn($param) => $param !== null);

        return !empty($queryParams) ? $url . '?' . http_build_query($queryParams) : $url;
    }

    static function parse(array $segments, array $query): ?self
    {
        $instance = new static();
        if (array_shift($segments) !== $instance->basePath) {
            return null;
        }

        foreach ($instance->segments as $position => &$property) {
            if (!isset($segments[$position])) {
                continue;
            }
            $property = self::coarseProperty(gettype($property), $segments[$position]);
        }
        foreach ($instance->queryParams as $name => &$property) {
            if (!isset($query[$name])) {
                continue;
            }
            $property = self::coarseProperty(gettype($property), $query[$name]);
        }

        return $instance->valid() ? $instance : null;
    }

    function valid(): bool
    {
        return true;
    }

}