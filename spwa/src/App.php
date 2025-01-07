<?php

namespace Spwa;

use Spwa\Http\HttpRequest;
use Spwa\Http\HttpResponse;
use Spwa\Http\MiddlewareHandler;

class App
{

    static function run(MiddlewareHandler|array $middleware): void
    {
        $request = new HttpRequest();
        $handlers = is_array($middleware) ? $middleware : [$middleware];

        // last handler is not found
        $execute = self::buildChain($handlers, $request, fn(HttpRequest $request) => HttpResponse::notFound());
        $response = $execute();
        $response->send();
    }

    private static function buildChain(array $handlers, HttpRequest $request, callable $final): callable
    {
        // idea is to start from the last middleware and
        // build a chain of callables moving backwards to the first middleware
        $next = $final;
        for ($i = count($handlers) - 1; $i >= 0; $i--) {
            $current = $handlers[$i];
            $next = fn() => $current->handle($request, $next);
        }
        return $next;
    }
}


