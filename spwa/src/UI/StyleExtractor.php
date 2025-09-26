<?php

namespace Spwa\UI;

use ArrayIterator;

const T_SINGLE = 0;


class StyleExtractor
{
    static Rule $rule;

    static function extract(string $filePath)
    {
        $source = file_get_contents($filePath);

        // remove all whitespace tokens
        $filtered = array_filter(
            token_get_all($source),
            fn($token) => !is_array($token) || $token[0] !== T_WHITESPACE
        );

        // conform to array format.
        $tokens = array_map(
            fn($token) => is_array($token) ? [$token[0], $token[1], token_name($token[0])] : [T_SINGLE, $token, "T_SINGLE"],
            $filtered
        );


        // map token ids to names
        // print_r(array_map(fn($token) => is_array($token) ? [token_name($token[0]), $token[1]] : [$token, ""], $tokens));

        self::processTokens(new ArrayIterator($tokens));
    }

    private static function processTokens(ArrayIterator $it): void
    {
        while ($it->valid()) {
            $hit = self::$rule->match($it);
            if ($hit != null)
                print_r($hit);

            $it->next();
        }
    }

}


StyleExtractor::$rule =   Rule::method(["background"], [
    Rule::static("Color", Rule::method(["gray", "green", "blue", "red", "black", "white"], [
        Rule::optional(Rule::number()),
        Rule::optional(Rule::number())
    ])),
]);
/*
.bg-blue-400\:hover\:active {
    background-color: #60a5fa;
}
*/


abstract class Rule
{
    abstract function match(ArrayIterator $it): mixed;

    static function number(): Rule
    {
        return new NumberRule();
    }

    static function optional(Rule $rule): Rule
    {
        return new OptionalRule($rule);
    }

    static function method(array $methods, array $params): Rule
    {
        return new MethodRule($methods, $params);
    }

    static function static(string $className, Rule $method): Rule
    {
        return new StaticRule($className, $method);
    }
}

class StaticRule extends Rule
{
    private string $className;
    private Rule $method;

    function __construct(string $className, Rule $method)
    {
        $this->className = $className;
        $this->method = $method;
    }

    function match(ArrayIterator $it): mixed
    {
        [$type, $val] = $it->current();
        if ($type != T_STRING || $val != $this->className) {
            return null;
        }
        $it->next();

        [$type, $val] = $it->current();
        if ($type != T_DOUBLE_COLON) {
            return null;
        }
        $it->next();
        $hit = $this->method->match($it);
        if ($hit === null) {
            return null;
        }
        return [$this->className, $hit];
    }
}

class MethodRule extends Rule
{
    private array $methods;
    private array $params;

    function __construct(array $methods, array $params)
    {
        $this->methods = $methods;
        $this->params = $params;
    }

    function match(ArrayIterator $it): mixed
    {
        [$type, $val] = $it->current();
        $index = array_search($val, $this->methods);
        if ($type != T_STRING || $index === false) {
            return null;
        }
        $it->next();

        [$type, $val] = $it->current();
        if ($type != T_SINGLE || $val != "(") {
            return null;
        }
        $it->next();

        $params = [];
        foreach ($this->params as $param) {
            $res = $param->match($it);
            if ($res === null) {
                break;
            }
            $params[] = $res;
        }

        [$type, $val] = $it->current();
        if ($type != T_SINGLE || $val != ")") {
            return null;
        }
        $it->next();

        return [$this->methods[$index], $params];
    }
}

class OptionalRule extends Rule
{
    private Rule $rule;

    function __construct(Rule $rule)
    {
        $this->rule = $rule;
    }

    function match(ArrayIterator $it): mixed
    {
        $res = $this->rule->match($it);
        if ($res === null) {
            return null;
        }
        return $res;
    }
}

class NumberRule extends Rule
{
    function match(ArrayIterator $it): mixed
    {
        [$type, $val] = $it->current();
        if ($type == T_LNUMBER) {
            $it->next();
            return intval($val);
        }
        return null;
    }
}