<?php

namespace Spwa\Template;

use InvalidArgumentException;

/**
 * @param Node|NodeAttribute[] $items
 * @return ElementNode
 */
function html(...$items): ElementNode
{
    return new ElementNode("html", $items);
}

/**
 * @param Node|NodeAttribute[] $items
 * @return ElementNode
 */
function body(...$items): ElementNode
{
    return new ElementNode("body", $items);
}

/**
 * @param Node|NodeAttribute[] $items
 * @return ElementNode
 */
function head(...$items): ElementNode
{
    return new ElementNode("head", $items);
}

function title(string $title): ElementNode
{
    return new ElementNode("title", [
        new TextNode($title)
    ]);
}

/**
 * @param Node|NodeAttribute[] $items ;
 * @return ElementNode
 */
function script(...$items): ElementNode
{
    return new ElementNode("script", $items);
}

/**
 * @param Node|NodeAttribute[] $items ;
 * @return ElementNode
 */
function style(...$items): ElementNode
{
    return new ElementNode("style", $items);
}

/**
 * @param Node|NodeAttribute[] $items ;
 * @return ElementNode
 */
function link(...$items): ElementNode
{
    return new ElementNode("link", $items);
}

/**
 * @param [string, string]][] $items
 * @return ElementNode
 */
function meta(...$pairs): ElementNode
{
    return new ElementNode("meta", array_map(fn($pair) => new NodeAttributeText($pair[0], $pair[1]), $pairs));
}

/**
 * @param Node|NodeAttribute[] $items
 * @return ElementNode
 */
function div(...$items): ElementNode
{
    return new ElementNode("div", $items);
}

/**
 * @param NodeAttribute[] $items
 * @return ElementNode
 */
function input(string $type, ...$items): ElementNode
{
    return new ElementNode("input", [type($type), ...$items]);
}

/**
 * @param Node|NodeAttribute[] $items
 * @return ElementNode
 */
function button(...$items): ElementNode
{
    return new ElementNode("button", $items);
}

/**
 * @template T
 * @param T[] $items
 * @param callable(T $item, int $index): Node $render
 * @param callable(T $item, int $index): string|int $key
 */
function _for(array $items, callable $key, callable $render): EachNode
{
    return new EachNode($items, $key, $render);
}

/**
 * Creates a new component instance and wraps it in an ElementNode.
 *
 * @template TProps
 * @param class-string<Component<TProps>> $class The class name of the component.
 * @param TProps $props The properties to pass to the component.
 * @return ComponentNode
 * @throws InvalidArgumentException If the class does not have a default constructor.
 */
function component(string $class, $props): ComponentNode
{
    // Ensure the class implements the DefaultConstructibleComponent interface
    if (!is_a($class, DefaultCtor::class, true)) {
        throw new InvalidArgumentException("Class $class must have a default constructor.");
    }

    return new ComponentNode($class, $props);
}

function text(string $text): TextNode
{
    return new TextNode($text);
}

function content(string $content): TextNode
{
    return new TextNode($content, false);
}


/**
 * @param string ...$class
 * @return NodeAttribute
 */
function _class(string ...$class): NodeAttribute
{
    // filter out empty strings
    $classes = implode(" ", array_filter($class));
    return new NodeAttributeText("class", $classes);
}

function onClick(callable $handler): NodeAttribute
{
    return new NodeAttributeEvent("click", $handler);
}

function src(string $src): NodeAttribute
{
    return new NodeAttributeText("src", $src);
}

function href(string $href): NodeAttribute
{
    return new NodeAttributeText("href", $href);
}

function type(string $type): NodeAttribute
{
    return new NodeAttributeText("type", $type);
}

function bind(&$value): NodeAttribute
{
    return new NodeAttributeBind($value);
}

