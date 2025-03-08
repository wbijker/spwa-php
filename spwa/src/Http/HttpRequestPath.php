<?php

namespace Spwa\Http;

class HttpRequestPath
{

    private string $path;
    private array $query = [];
    private array $segments;

    public function __construct(private string $_uri)
    {
        $parsedUrl = parse_url($this->_uri);
        $this->path = $parsedUrl['path'] ?? '/'; // Defaults to '/' if empty
        parse_str($parsedUrl['query'] ?? '', $this->query);
        $this->segments = explode('/', $this->path);
    }

    function uri(): string
    {
        return $this->_uri;
    }

    function queryParams(): array
    {
        return $this->query;
    }

    function query(string $key): string
    {
        return $this->query[$key] ?? '';
    }

    function getSegments(): array
    {
        return $this->segments;
    }

    function startWithSegment(array $segments): bool
    {
        $uriSegments = $this->segments;
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
}