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
    private $renderFn;

    /**
     * @param T[] $items
     * @param (callable(T $item, int $index): string|int)|null $key
     * @param callable(T $item, int $index): Node $render
     */
    public function __construct(array $items, ?callable $key, callable $render)
    {

        $this->items = $items;
        $this->key = $key;
        $this->renderFn = $render;
    }

    function render(NodePath $path, PathState $state): HtmlNode
    {
        $children = [];
        foreach ($this->items as $index => $item) {
            $key = $this->key ? ($this->key)($item, $index) : null;
            $output = ($this->renderFn)($item, $index);
            $children[] = new KeyedNode($key, $output->render($path->next($index), $state));
        }
        return new HtmlEach($this, $path, $children);
    }
}