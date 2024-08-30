<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlEach;
use Spwa\Dom\HtmlNode;
use Spwa\Dom\KeyedNode;

/**
 * @template T
 */
class EachNode extends Node
{
    /**
     * @var T[] $items
     */
    private array $items;
    /**
     * @var (callable(T $item, int $index): string|int)|null
     */
    private $key;
    /**
     * @var callable(T $item, int $index): Node
     */
    private $render;

    /**
     * @param T[] $items
     * @param (callable(T $item, int $index): string|int)|null $key
     * @param callable(T $item, int $index): Node $render
     */
    public function __construct(array $items, ?callable $key, callable $render)
    {

        $this->items = $items;
        $this->key = $key;
        $this->render = $render;
    }

    function render(NodePath $path, PathState $state): HtmlNode
    {
        $nodes = array_map($this->render, $this->items, array_keys($this->items));
        return new HtmlEach($this, $path, array_map(function($item, $index) use ($state, $path) {
            // if no key function is provided, use null key
            $key = $this->key ? ($this->key)($item, $index) : null;
            return new KeyedNode($key, $item->render($path->next($index), $state));
        }, $nodes, array_keys($nodes)));
    }
}