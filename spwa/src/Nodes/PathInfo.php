<?php

namespace Spwa\Nodes;

class PathInfo
{
    public function __construct(public int $domIndex, public string|int|bool|null $key)
    {
    }

    function set(Node $node, ?Node $parent, bool $add): void
    {
        // root node
        if ($parent == null) {
            $node->path = [];
            $node->key = [$this->key];
            return;
        }
        // real dom
        if ($add) {
            $node->path = array_merge($parent->path, [$this->domIndex]);
            $node->key = array_merge($parent->key, [$this->key]);
            return;
        }
        // node is a virtual node only, such as ForNode, IfNode
        $node->path = $parent->path;
        $node->key = array_merge($parent->key, [$this->key]);
    }
}