<?php

namespace Spwa\Nodes;

use Spwa\Js\JS;

class ForNode extends Node
{
    /**
     * @template T
     * @param array<T> $list Array of items of type T
     * @param callable(T $item, int $index): (string|int|bool) $keyCallback A callback that generates a key for each item
     * @param callable(T $item, int $index): Node $renderCallback A callback that renders a Node for each item
     */
    public function __construct(private array $list, private $keyCallback, private $renderCallback)
    {
    }

    function compare(Node $node, PatchBuilder $patch): void
    {
        // TODO: Implement compare() method.
    }

    function renderHtml(): string
    {
        $list = array_map(fn($node) => $node[1]->renderHtml(), $this->getNode());
        return implode("", $list);
    }

    // [$key string|int|bool, Node $node][]
    private ?array $node = null;

    public function getNode(): array
    {
        if ($this->node != null)
            return $this->node;

        $this->node = [];
        foreach ($this->list as $i => $item) {
            $key = ($this->keyCallback)($item, $i);
            $node = ($this->renderCallback)($item, $i);
            $this->node[] = [$key, $node];
        }
        return $this->node;
    }

    function initialize(?Node $parent, PathInfo $path, StateManager $manager): void
    {
        $children = $this->getNode();

        foreach ($children as $index => [$key, $node]) {
            $node->initialize($this, $this->path->addChild($key), $manager);
        }
    }

    function finalize(StateManager $manager): void
    {
    }
}