<?php

namespace Spwa\Http;

class HttpRequest
{

    var HttpRequestPath $path;

    public function __construct()
    {
        $this->path = new HttpRequestPath($_SERVER['REQUEST_URI']);
    }

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

    function readJson(bool $associative): ?array
    {
        $json = file_get_contents('php://input');
        if (gettype($json) !== 'string') {
            return null;
        }
        return json_decode($json, $associative);
    }

}