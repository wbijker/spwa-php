<?php

namespace Spwa;

use Spwa\Template\Node;
use Spwa\Template\TextNode;

class App
{
    static function render(Node $component): void
    {
        print_r($component);

        echo $component->render();
        // first need to execute to html nodes to determine the order of htmlNodes
        // we don't need to have separate nodes and html nodes
        // leave at only html nodes
        // the rendering function can be overridden in the html node

        // html nodes can have path positions
        // html node contains a link to the original template node.
        // HtmlTagNode -> ElementNode
        // HtmlTextNode -> TextNode
    }
}

abstract class Patch
{
    public Node $node;

    /**
     * @param Node $node
     */
    function __construct(Node $node)
    {
        $this->node = $node;
    }

    abstract function type(): string;

    abstract function data(): mixed;

    function pack()
    {
        return [
            "type" => $this->type(),
            "path" => $this->node->path,
            "data" => $this->data()
        ];
    }


}

class TextPatch extends Patch
{
    private string $replaceText;

    /**
     * @param string $replaceText
     */
    public function __construct(TextNode $node, string $replaceText)
    {
        // call base constructor
        parent::__construct($node);
        $this->replaceText = $replaceText;
    }

    function type(): string
    {
        return "text";
    }

    function data(): mixed
    {
        return $this->replaceText;
    }
}


function compareNode(Node $old, Node $new)
{
//    if ($old instanceof Component) {
//        echo "Node 1 is an instance of Component\n";
//    }
//    if ($old instanceof TextNode) {
//        echo "Node 1 is an instance of Node\n";
//    }
//    if ($old instanceof ElementNode) {
//        echo "Node 1 is an instance of HtmlNode\n";
//    }
}