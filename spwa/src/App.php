<?php

namespace Spwa;

use Spwa\Template\Node;
use Spwa\Template\NodePath;
use Spwa\Template\TextNode;

class App
{
    static function render(Node $component): void
    {
        $dom = $component->render(new NodePath([0]));
        echo $dom->render();
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
//            "path" => $this->node->path,
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