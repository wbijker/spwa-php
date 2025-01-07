<?php

namespace Spwa\Http;

interface MiddlewareHandler
{
    function handle(HttpRequest $request, callable $next): HttpResponse;
}

