<?php

namespace Spwa\Route;

use Spwa\Http\HttpRequestPath;
use Spwa\Js\Console;
use function App\Components\convert;

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
    /**
     * @throws \Exception
     */
    public function toUrl($instance): string
    {
        if (get_class($instance) != $this->class) {
            throw new \Exception("Route path class must be instance of $this->class");
        }

        // replace all members of the class in pattern
        $values = get_object_vars($instance);
        $ret = $this->path;
        foreach ($values as $key => $value) {
            $ret = str_replace("{{$key}}", $value, $ret);
        }
        return $ret;
    }


    public function match(HttpRequestPath $path): ?array
    {
        $urlParts = $path->getSegments();
        $patternParts = explode("/", $this->path);

        if (count($urlParts) != count($patternParts)) {
            return null;
        }

        $ret = [];

        for ($i = 0; $i < count($urlParts); $i++) {
            $vars = [];
            $text = [];
            $urlPart = $urlParts[$i];
            $last = 0;
            $patternPart = $patternParts[$i];

            $matches = [];
            preg_match_all('/\{(.+?)}/', $patternPart, $matches, PREG_OFFSET_CAPTURE);
            if (count($matches[1]) == 0) {
                // check static part
                if ($urlPart != $patternPart)
                    return null;
                continue;
            }

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
                return null;
            }

            $values = getBetween($urlPart, $text);
            for ($j = 0; $j < count($vars); $j++) {
                $ret[$vars[$j]] = $values[$j];
            }
        }

        return $ret;
    }

}