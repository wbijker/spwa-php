<?php

namespace Spwa\Nodes;

use Spwa\Js\JS;

class PathInfo
{
    private function __construct(

        public ?PathInfo            $parent,
        public int                  $index,
        public string|int|bool|null $key = null,
        public ?string              $instance = null)
    {
        $this->indexPath = $parent == null ? [] : array_merge($parent->indexPath, [$index]);
        $this->keyPath = $parent == null ? [] : array_merge($parent->keyPath, [$key ?? $index]);
    }

    public array $children = [];
    // materialize version of the path and key
    // path is an array of integers that represent the index paths of the node in the DOM tree.
    public array $indexPath = [];
    // The key is an array that uniquely represents the key of the node used in the diffing algorithm, and state management.
    public array $keyPath = [];

    public function set(string|int|bool|null $key = null, ?string $instance = null): PathInfo
    {
        if ($key != null)
            $this->keyPath[count($this->keyPath) - 1] = $key;

        $this->instance = $instance;
        return $this;
    }

    public function addChild(string|int|bool|null $key = null, ?string $instance = null): PathInfo
    {
        $index = count($this->children);
        $child = new PathInfo($this, $index, $key, $instance);
        $this->children[] = $child;
        return $child;
    }

    // the starting point of the hierarchy
    // It starts at the body of the document
    static function root(?string $key = null): PathInfo
    {
        return new PathInfo(null, 0, $key);
    }

    function pathStr(): string
    {
        return implode("|", $this->indexPath);
    }

    function keyStr(): string
    {
        return implode("|", $this->keyPath);
    }

}