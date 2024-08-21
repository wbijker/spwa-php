<?php

namespace Spwa\Template;

/**
 * @param Node|NodeAttribute[] $items
 * @return ElementNode
 */
function div(...$items): ElementNode
{
    return new ElementNode("div", $items);
}

function text(string $text): TextNode
{
    return new TextNode($text);
}

function _class(string ...$class): NodeAttribute
{
    // filter out empty strings
    $classes = implode(" ", array_filter($class));
    return new NodeAttribute("class", $classes);
}
