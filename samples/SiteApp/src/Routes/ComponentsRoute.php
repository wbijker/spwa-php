<?php

namespace Samples\SiteApp\Routes;

use Spwa\UI\BaseRoute;

class ComponentsRoute extends BaseRoute
{
    public static function handle(string $uri): ?static
    {
        return $uri === '/components' ? new static() : null;
    }

    public function toUrl(): string
    {
        return '/components';
    }
}
