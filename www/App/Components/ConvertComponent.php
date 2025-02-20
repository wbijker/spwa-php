<?php

namespace App\Components;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use Spwa\Html\Div;
use Spwa\Html\TextArea;
use Spwa\Js\JS;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;


function convertToComponent(DOMNode $node): string
{
    if ($node instanceof DOMText) {
        return 'new HtmlText(' . var_export(trim($node->textContent), true) . ')';
    }

    if (!$node instanceof DOMElement) {
        return '';
    }

    $tag = ucfirst($node->nodeName);
    $attributes = [];
    $children = [];

    // Collect attributes
    foreach ($node->attributes as $attr) {
        $attributes[] = "{$attr->name}: " . var_export($attr->value, true);
    }

    // Collect children
    foreach ($node->childNodes as $child) {
        $children[] = convertToComponent($child);
    }

    // Remove empty children
    $children = array_filter($children);

    $childrenCode = empty($children) ? '' : 'children: [' . implode(', ', $children) . ']';

    // Merge attributes and children
    $params = array_filter([$childrenCode, ...$attributes]);
    return "new {$tag}(" . implode(', ', $params) . ")";
}

function convert(string $html): string
{
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $fragment = $dom->createDocumentFragment();
    $fragment->appendXML($html);

    $components = [];

    foreach ($fragment->childNodes as $node) {
        if ($node instanceof DOMElement) {
            $components[] = convertToComponent($node);
        }
    }

    return implode(",\n", $components) . ";";
}

class ConvertComponent extends Component
{
    private static string $code = "";

    function render(): Node
    {
        return new Div(class: "w-screen h-screen flex", children: [
            new Div(class: "m-auto bg-red-200 p-2", children: [
                new Div(children: [new HtmlText("Converter")]),
                new Div(class: "flex", children: [
                    new TextArea(rows: 10, cols: 50, onChange: fn($value) => self::$code = convert($value)),
                    new Div(class: "m-2 cursor-pointer", children: [new HtmlText("=>")]),

                    new TextArea(rows: 10, cols: 50, value: self::$code),
                ]),
            ])
        ]);
    }

}