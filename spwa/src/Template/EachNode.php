<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlEach;
use Spwa\Dom\HtmlNode;

/**
 * @template T
 */
class EachNode extends Node
{
    private array $items;
    /**
     * @var callable|int
     */
    private $key;
    /**
     * @var callable
     */
    private $render;

    /**
     * @param T[] $items
     * @param callable(T $item, int $index): string|int $key
     * @param callable(T $item, int $index): Node $render
     */
    public function __construct(array $items, callable $key, callable $render)
    {

        $this->items = $items;
        $this->key = $key;
        $this->render = $render;
    }

    function render(NodePath $path, PathState $state): HtmlNode
    {
        $nodes = array_map($this->render, $this->items, array_keys($this->items));
        return new HtmlEach($this, $path, array_map(fn($item, $index) => $item->render($path->next($index), $state), $nodes, array_keys($nodes)));
    }
}