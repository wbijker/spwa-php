<?php
require_once 'levenshtein.php';

const UPDATE_TEXT = 0;
const UPDATE_ATTR = 1;
const DELETE_NODE = 2;
const INSERT_NODE = 3;
const DELETE_ATTR = 4;

function removeEmptyValues(array $array): array
{
    return array_filter($array, fn($value) => !empty($value));
}

interface PhpObject
{
    function render(): string;
}

class PhpFunction implements PhpObject
{
    public string $name;
    public array $args;

    /**
     * @param string $name
     * @param array $args
     */
    public function __construct(string $name, array $args)
    {
        $this->name = $name;
        $this->args = $args;
    }


    function render(): string
    {
        $args = implode(", ", array_map(fn($arg) => Php::render($arg), $this->args));
        return "$this->name($args)";
    }
}

class PhpLiteral implements PhpObject
{
    public string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    function render(): string
    {
        return $this->value;
    }
}

class Php
{
    static function literal($str): PhpLiteral
    {
        return new PhpLiteral($str);
    }

    static function function (string $name, array $args): PhpFunction
    {
        return new PhpFunction($name, $args);
    }

    static function render($data): string
    {
        if (is_string($data)) {
            return "\"$data\"";
        }
        if ($data instanceof PhpObject) {
            return $data->render();
        }
        if (is_null($data)) {
            return "null";
        }
        if (is_numeric($data)) {
            return $data;
        }
        if (is_bool($data)) {
            return $data ? "true" : "false";
        }
        if (is_array($data)) {
            $mapped = array_map(function ($key, $value) {
                if (is_numeric($key)) {
                    return self::render($value);
                }
                return self::render($key) . " => " . self::render($value);
            }, array_keys($data), $data);

            return "[" . implode(", ", $mapped) . "]";
        }

        return "null";
    }
}


class PhpArray
{
    private array $array = [];

    /**
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }


    function set($key, $value): void
    {
        $this->array[$key] = $value;
    }

    function render(bool $ignoreEmpty): string
    {
        $mapped = array_map(function ($key, $value) use ($ignoreEmpty) {
            if ($value instanceof PhpArray) {
                if ($ignoreEmpty && count($value->array) == 0)
                    return null;

                return "\"$key\" => " . $value->render($ignoreEmpty);
            }

            if ($ignoreEmpty && $value == null)
                return null;

            return "\"$key\" => $value";
        }, array_keys($this->array), $this->array);

        // filter out null values
        $mapped = array_filter($mapped, fn($value) => $value != null);

        return "[" . implode(", ", $mapped) . "]";
    }
}

function compare($prev, $next, &$list): void
{
    // $prev and $next should always be of the same type
    if (get_class($prev) != get_class($next)) {
        return;
    }

    $prev->compare($next, $list, $list);
}


function classNames(...$classes): string
{
    // remove empty values
    return implode(" ", array_filter($classes, fn($value) => !empty($value)));
}

function buildAttr($attrs): string
{
    // categorize attributes
    $list = [];
    $events = [];
    $bound = [];
    $ignore = false;

    foreach ($attrs as $name => $value) {

        if (substr($name, 0, 1) == '@') {
            $name = substr($name, 1);

            if ($name == 'click' || $name == 'keydown') {
                $events[$name] = Php::literal("fn() => " . $value[0]);
                continue;
            }

            if ($name == 'ignore') {
                $ignore = true;
                continue;
            }

            if ($name == 'bound') {
                // remove $model from bound
                $prop = extractBound($value[0]);
                if ($prop != null) {
                    $bound = $prop;
                    $list['value'] = $value[0];

                }
                continue;
            }

            // only class can have multiple values
            if ($name == 'class') {
                $list[$name] ??= [];
                $list[$name][] = Php::literal(implode(", ", $value));
            } else {
                $list[$name] = Php::literal($value[0]);
            }
            continue;
        }


        if ($name == 'class') {
            $list[$name] ??= [];
            $list[$name][] = implode(", ", $value);
        } else {
            $list[$name] = $value[0];
        }
    }

    // remove array for all single items
    foreach ($list as $key => $value) {
        if (is_array($value)) {

            if (count($value) == 1) {
                $list[$key] = $value[0];
            } else {
                $list[$key] = Php::function("classNames", $value);
            }
        }
    }

    return Php::render(removeEmptyValues([
        "attrs" => $list,
        "events" => $events,
        "bound" => $bound,
        "ignore" => $ignore
    ]));
}


function indent($index, $first = false): string
{
    return str_repeat(" ", $first ? 0 : $index * 4);
}

function buildNode(HtmlTagNode $node, $index = 0, $first = false): string
{
    $children = array_map(fn($child) => buildTree($child, $index + 1, false), $node->children);
    $c = implode("," . PHP_EOL, $children);

    return indent($index, $first) . "TemplateNode::html(\"" . $node->name . "\", " . buildAttr($node->attributes) . ", [" . PHP_EOL . $c . PHP_EOL . indent($index) . "])";
}

function getAttr(HtmlTagNode $node, string $attr): ?string
{
    $values = $node->getAndRemoveAttr($attr);
    if (empty($values))
        return null;

    return $values[0];
}

function extractBound(string $str): ?string
{
    // for now simple text binding like $model->name
    // but in future we can support more complex expressions
    if (preg_match('/\$model->(\w+)/', $str, $matches)) {
        // $matches[1] is the word
        return $matches[1];
    }

    return null;
}

function extractForVars(string $str): array
{
    $pattern = '/(\S+)\s+as\s+(\S+)(\s+=>\s+(\S+))?/';

    if (preg_match($pattern, $str, $matches)) {
        // Format: $exp as $item
        if (count($matches) == 3) {
            return ["$matches[1]", "$matches[2]", '$index'];
        }
        if (count($matches) == 5) {
            return ["$matches[1]", "$matches[4]", "$matches[2]"];
        }
    }

    // Format not recognized
    return [null, null, null];
}


function buildTree(HtmlDomNode $node, $index = 0, $first = false): ?string
{
    $indent = indent($index, $first);

    if ($node instanceof HtmlTagNode) {

        // handle special attributes cases
        $ifExp = getAttr($node, '@if');
        if ($ifExp != null) {
            $then = buildNode($node, $index, true);
            return $indent . "TemplateNode::if($ifExp, " . $then . ", null)";
        }

        $for = getAttr($node, '@for');
        if ($for != null) {
            // for should always use syntax
            $vars = extractForVars($for);
            return $indent . "TemplateNode::for($vars[0], fn($vars[1], $vars[2]) => " . buildNode($node, $index, true) . ")";
        }

//        $bound = getAttr($node, '@bound');
//        if ($bound != null) {
//            // add value attribute to fill initial value
//            $node->setAttribute('value', $bound);
//        }

        return buildNode($node, $index, $first);
    }

    if ($node instanceof HtmlTextNode) {
        // replace newline with \n
        // and carriage return with \r
        $text = str_replace("\n", "\\n", $node->text);
        $text = str_replace("\r", "\\r", $text);
        return $indent . "TemplateNode::text(\"" . $text . "\")";
    }

    return null;
}


