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
    abstract function render(NodePath $path, EventListeners $listeners): HtmlNode;

    static function compareNode(Node $old, Node $new): void
    {
        // get_class($old) == get_class($new)
        // $old->compare($new);
        if ($old instanceof ElementNode && $new instanceof ElementNode) {

            $old->compare($new);
            return;
        }

        if ($old instanceof TextNode && $new instanceof TextNode) {
            $old->compare($new);
            return;
        }

        if ($old instanceof Component && $new instanceof Component) {
            $old->compare($new);
            return;
        }
    }
}

