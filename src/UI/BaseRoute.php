<?php

namespace BrickPHP\UI;

/**
 * Base contract for a route. Each concrete route is its own class and owns
 * both directions of the relationship:
 *
 *   handle(uri)  – try to parse the URI into an instance, or return null
 *   toUrl()      – emit the URL for this instance
 *
 * Keeping both on the same class avoids the usual split between "route
 * pattern" and "url generator" — they can't drift out of sync.
 *
 * Example:
 *
 *   class ArticleRoute extends BaseRoute {
 *       public function __construct(public string $slug) {}
 *
 *       public static function handle(string $uri): ?static {
 *           if (preg_match('#^/article/([a-z0-9-]+)$#', $uri, $m)) {
 *               return new static($m[1]);
 *           }
 *           return null;
 *       }
 *
 *       public function toUrl(): string { return '/article/' . $this->slug; }
 *   }
 */
abstract class BaseRoute
{
    abstract public static function handle(string $uri): ?static;

    abstract public function toUrl(): string;
}
