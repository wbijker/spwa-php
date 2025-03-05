<?php

namespace Spwa\Route;

use Spwa\Http\HttpRequestPath;
use Spwa\Js\Console;

function getBetween(string $str, array $arr): array
{
    $result = [];
    $offset = 0;

    foreach ($arr as $index => $token) {
        $pos = strpos($str, $token, $offset);
        if ($pos === false) break;

        // Only add the in-between substring once we've passed the first token
        if ($index > 0) {
            $result[] = substr($str, $offset, $pos - $offset);
        }
        // Move offset to the end of the matched token
        $offset = $pos + strlen($token);
    }
    if ($offset < strlen($str))
        $result[] = substr($str, $offset);

    return $result;
}


/*
 * @template T extends RouteParams
 */

class RoutePath
{
    /*
     * @param string $path
     * @param class-string<T> $class
     */
    public function __construct(public string $path, public $class)
    {
    }

    /*
     * @param T $instance
     */
    public function toUrl($instance): string
    {
        return "";
    }


    public function match(HttpRequestPath $path): ?array
    {
        $url = "/products/seek-cat-electronics-4kw-gauteng-deal/44";
        $p = "/products/{listing}-cat-{category}-{rating}-{place}/{id}";

        $urlParts = explode("/", $url);
        $patternParts = explode("/", $p);
        if (count($urlParts) != count($patternParts)) {
            return null;
        }

        for ($i = 0; $i < count($urlParts); $i++) {
            $vars = [];
            $text = [];
            $urlPart = $urlParts[$i];
            $last = 0;
            $patternPart = $patternParts[$i];

            $matches = [];
            preg_match_all('/\{(.+?)}/', $patternPart, $matches, PREG_OFFSET_CAPTURE);
            if (count($matches[1]) == 0)
                // check static part
                continue;

            foreach ($matches[1] as $match) {
                [$var, $pos] = $match;
                $vars[] = $var;
                // string between
                $between = substr($patternPart, $last, $pos - $last - 1);
                $text[] = $between;
                $last = $pos + strlen($var) + 1;
            }
            if ($last < strlen($patternPart))
                $text[] = substr($patternPart, $last);

            if (count($vars) != count($text)) {
                die("bom");
                return null;
            }

            print_r($vars);
            print_r($text);
            print_r(getBetween($urlPart, $text));
        }

        die();

        Console::log("Trying to match " . $this->path . " with " . $path->uri());
        return null;
    }

}