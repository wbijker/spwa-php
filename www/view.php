<?php
require_once 'levenshtein.php';

const UPDATE_TEXT = 0;
const UPDATE_ATTR = 1;
const DELETE_NODE = 2;
const INSERT_NODE = 3;
const DELETE_ATTR = 4;


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
    if (empty($attrs) || count($attrs) == 0)
        return "null";

    // todo: handle multiple attr values
    $mapped = array_map(function ($key, $values) {

        if ($key == "@click" || $key == "@keydown") {
            // need to store the event handler within
            return "\"" . $key . "\" => fn() => " . $values[0];
        }

        return "\"" . $key . "\" => \"" . $values[0] . "\"";

    }, array_keys($attrs), $attrs);

    return "[" . implode(", ", $mapped) . "]";
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

        $bound = getAttr($node, '@bound');
        if ($bound != null) {
            // add value attribute to fill initial value
            $node->setAttribute('value', $bound);
        }

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


