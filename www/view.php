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

    $arr = iterator_to_array($attrs);
    $attrs = array_map(function ($attr) {

        if ($attr->name == "click" || $attr->name == "keydown") {
            // need to store the event handler within
            return "\"" . $attr->name . "\" => fn() => " . $attr->value;
        }

        return "\"" . $attr->name . "\" => \"" . $attr->value . "\"";
    }, $arr);
    return "[" . implode(", ", $attrs) . "]";
}


function indent($index, $first = false): string
{
    return str_repeat(" ", $first ? 0 : $index * 4);
}

function buildNode(DOMNode $node, $index = 0, $first = false): string
{
    $children = array_map(fn($child) => buildTree($child, $index + 1, false), iterator_to_array($node->childNodes));
    $c = implode("," . PHP_EOL, $children);

    return indent($index, $first) . "TemplateNode::html(\"" . $node->tagName . "\", " . buildAttr($node->attributes) . ", [" . PHP_EOL . $c . PHP_EOL . indent($index) . "])";
}

function getAttr(DOMElement $node, string $attr, bool $remove): ?string
{
    $value = $node->attributes->getNamedItem($attr);
    if ($value == null)
        return null;

    if ($remove)
        $node->removeAttribute($attr);
    return $value->value;
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


function buildTree(DOMNode $node, $index = 0, $first = false): ?string
{
    $indent = indent($index, $first);

    if ($node instanceof DOMElement) {

        // handle special attributes cases
        $ifExp = getAttr($node, 'if', true);
        if ($ifExp != null) {
            $then = buildNode($node, $index, true);
            return $indent . "TemplateNode::if($ifExp, " . $then . ", null)";
        }

        $for = getAttr($node, 'for', true);
        if ($for != null) {
            // for should always use syntax
            $vars = extractForVars($for);
            return $indent . "TemplateNode::for($vars[0], fn($vars[1], $vars[2]) => " . buildNode($node, $index, true) . ")";
        }

        $bound = getAttr($node, 'bound', false);
        if ($bound != null) {
            // add value attribute to fill initial value
            $node->setAttribute('value', "\$model->" . $bound);
        }

        return buildNode($node, $index, $first);
    }

    if ($node instanceof DOMText) {
        // replace newline with \n
        // and carriage return with \r
        $text = str_replace("\n", "\\n", $node->textContent);
        $text = str_replace("\r", "\\r", $text);
        return $indent . "TemplateNode::text(\"" . $text . "\")";
    }

    return null;
}


