<?php

namespace Spwa\Template;

/*
The base class for all template nodes.
Templates nodes include building blocks for html: ElementNode, TextNode, ComponentNode, AttrNode
and programmable nodes: IfNode, ForNode
 */

use Spwa\Html\HtmlNode;
use Spwa\Html\HtmlTagNode;

abstract class Node
{
    abstract function execute(): HtmlNode;
}

