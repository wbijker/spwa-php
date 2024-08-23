<?php

namespace Spwa\Template;

/*
The base class for all template nodes.
Templates nodes include building blocks for html: ElementNode, TextNode, ComponentNode, AttrNode
and programmable nodes: IfNode, ForNode
 */

abstract class Node
{
    public function __construct()
    {
        $this->path = new NodePath([]);
    }

    abstract function render(): string;
    abstract function resolvePaths(NodePath $path): void;

    /**
     * The path in DOM to where this node is located.
     * [2,1,0] means root.children[2].children[1].children[0]
     * @var NodePath
     */
    public NodePath $path;


}

