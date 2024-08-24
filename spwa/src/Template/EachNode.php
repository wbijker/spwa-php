<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlFragment;
use Spwa\Dom\HtmlNode;

/**
 * @template T
 */
class EachNode extends Node
{
    /**
     * @var Node[] $nodes
     */
    private $nodes;

    /**
     * @param T[] $items
     * @param callable(T $item, int $index): Node $itemRender
     */
    public function __construct(array $items, callable $itemRender)
    {
        $this->nodes = array_map($itemRender, $items, array_keys($items));
    }

    function render(NodePath $path, EventListeners $listeners): HtmlNode
    {
        return new HtmlFragment($this, $path, array_map(fn($item, $index) => $item->render($path->next($index), $listeners), $this->nodes, array_keys($this->nodes)));
    }
}