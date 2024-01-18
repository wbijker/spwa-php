<?php
require_once 'levenshtein.php';

const UPDATE_TEXT = 0;
const UPDATE_ATTR = 1;
const DELETE_NODE = 2;
const INSERT_NODE = 3;
const DELETE_ATTR = 4;


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
                $events[$name] = "fn() => " . $value[0];
                continue;
            }

            if ($name == 'ignore') {
                $ignore = true;
                continue;
            }

            if ($name == 'bound') {
                $found = extractBound($value[0]);
                if ($found != null) {
                    $items = array_map(function($i) {
                        if (is_string($i))
                            return "\"$i\"";
                        return $i;
                    }, $found);

                    $bound = "[".implode(", ", $items)."]";
                    $access = implode(",", array_map(fn($i) => "[$i]", array_slice($found, 1)));

                    $list['value'] = '$model->' . $found[0] . $access;
                }
                continue;
            }

            $list[$name] = $value[0] ?? "true";
            continue;
        }

        $list[$name] = "\"$value[0]\"";
    }

    $arr = new PhpArray([
        "attrs" => new PhpArray($list),
        "events" => new PhpArray($events),
        "bound" => $bound,
        "ignore" => $ignore
    ]);
    return $arr->render(true);
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

function extractBound(string $str): ?array
{
    // you can bind to primitive and array
    // Step 1: Match the word and the entire bracketed section
    if (preg_match('/\$model->(\w+)((?:\[\d+\])*)/', $str, $matches)) {
        // $matches[1] is the word
        $result = [$matches[1]];

        // Step 2: Extract the numbers from the bracketed section
        if (preg_match_all('/\[(\d+)\]/', $matches[2], $numberMatches)) {
            // Merge the numbers into the result array
            $result = array_merge($result, $numberMatches[1]);
        }
        return $result;
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


