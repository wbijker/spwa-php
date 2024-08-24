<?php

namespace Spwa\Template;

/**
 * @template T
 */
class EachNode extends Node
{
//    /**
//     * @var T[] $items ;
//     */
//    private array $items;
//
//    /**
//     * @var callable(T $item, int $index): Node $itemRender
//     */
//    private $itemRender;
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
//        $this->items = $items;
//        $this->itemRender = $itemRender;

        $this->nodes = array_map($itemRender, $items, array_keys($items));
    }

    function resolvePaths(NodePath $path): void
    {
        // $path [0,2,0]
        parent::resolvePaths($path);
        foreach ($this->nodes as $index => $node) {
            $node->resolvePaths($path->set($index));
        }
    }

    function render(): string
    {
        return implode("", array_map(fn($item) => $item->render(), $this->nodes));
    }
}