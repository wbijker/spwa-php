<?php

namespace Spwa\Route;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

function extract(string $pattern, string $route, array $query): ?array
{
    $patternParts = explode('/', trim($pattern, '/'));
    $routeParts = explode('/', trim($route, '/'));

    if (count($patternParts) !== count($routeParts)) {
        return null;
    }

    $parts = [];
    foreach ($patternParts as $index => $part) {
        if (preg_match('/:(\w+)/', $part, $matches)) {
            $key = $matches[1]; // Extract the dynamic key name
            $parts[$key] = preg_replace('/' . preg_quote($part, '/') . '/', '', $routeParts[$index], 1);
        }
    }

    foreach ($query as $name) {
        $part[$name] = $_GET[$name] ?? null;
    }

    return $parts;
}

function coarsePart(?ReflectionType $type, &$ref, $value): void
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


abstract class RouteBase
{

    public static function tryMatch(string $route): ?static
    {
        $reflection = new ReflectionClass(self::class);
        $routeAttr = $reflection->getAttributes(Route::class)[0] ?? null;
        $route = $routeAttr->newInstance();
        $parts = extract($pattern, $route->path, $route->query);
        if ($parts === null) {
            return null;
        }
        return static::make($parts);
    }

    static function make(array $parts): static
    {
        $instance = new static();
        $reflection = new ReflectionClass(self::class);
        foreach ($reflection->getProperties() as $property) {
            $name = $property->getName();
            $type = $property->getType();
            coarsePart($type, $instance->$name, $parts[$name] ?? null);
        };
        return $instance;
    }

    function toUrl(): string
    {
        // return !empty($queryParams) ? $url . '?' . http_build_query($queryParams) : $url;
        return "";
    }
}