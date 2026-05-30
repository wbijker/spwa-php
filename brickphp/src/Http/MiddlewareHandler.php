<?php

namespace BrickPHP\Http;

interface MiddlewareHandler
{
    function handle(HttpRequest $request, callable $next): HttpResponse;
}

