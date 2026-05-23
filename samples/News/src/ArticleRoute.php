<?php

namespace Samples\News;

use Spwa\UI\BaseRoute;

/**
 * Matches `/article/<slug>` and carries the slug to the route handler. The
 * handler is responsible for resolving the slug to an Article (e.g. via
 * NewsData::findBySlug) and rendering the detail view.
 */
class ArticleRoute extends BaseRoute
{
    public function __construct(
        public string $slug,
    ) {}

    public static function handle(string $uri): ?static
    {
        if (preg_match('#^/article/([a-z0-9-]+)$#', $uri, $m)) {
            return new static($m[1]);
        }
        return null;
    }

    public function toUrl(): string
    {
        return '/article/' . $this->slug;
    }
}
