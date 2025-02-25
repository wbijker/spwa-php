<?php

namespace Spwa\Http;

class HttpRequest
{

    function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    function isGet(): bool
    {
        return $this->method() == 'GET';
    }

    function isPost(): bool
    {
        return $this->method() == 'POST';
    }

    function uri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    function segments(): array
    {
        $path = parse_url($this->uri(), PHP_URL_PATH); // Removes query parameters
        return array_values(array_filter(explode('/', $path), fn($segment) => $segment !== ''));
    }

    function queryParams(): array
    {
        return $_GET;
    }

    function query(string $key): string
    {
        return $_GET[$key] ?? '';
    }

    function startWithSegment(array $segments): bool
    {
        $uriSegments = $this->segments();
        $count = count($segments);
        if (count($uriSegments) < $count) {
            return false;
        }
        for ($i = 0; $i < $count; $i++) {
            if ($segments[$i] != $uriSegments[$i]) {
                return false;
            }
        }
        return true;
    }

    function readJson(bool $associative): array
    {
        return json_decode(file_get_contents('php://input'), $associative);
    }

}