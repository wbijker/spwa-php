<?php

namespace Spwa\Template;

/*
The base class for all template nodes.
Templates nodes include building blocks for html: ElementNode, TextNode, ComponentNode, AttrNode
and programmable nodes: IfNode, ForNode
 */

use Spwa\Dom\HtmlNode;

abstract class Node
{
    abstract function render(NodePath $path, PathState $state): HtmlNode;

}

