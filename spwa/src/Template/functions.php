<?php

namespace Spwa\Template;

use InvalidArgumentException;

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