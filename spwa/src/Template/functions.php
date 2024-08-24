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

/**
 * @template T
 * @param T[] $items
 * @param callable(T $item, int $index): Node $render
 */
function _for(array $items, callable $render): EachNode
{
    return new EachNode($items, $render);
}

function text(string $text): TextNode
{
    return new TextNode($text);
}

function _class(string ...$class): NodeAttribute
{
    // filter out empty strings
    $classes = implode(" ", array_filter($class));
    return new NodeAttributeText("class", $classes);
}

// events
function onClick(callable $handler): NodeAttribute
{
    return new NodeAttributeText("onclick", "event handler");
}