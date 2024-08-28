<?php

namespace Spwa;

use Spwa\Template\Component;
use Spwa\Template\PathState;
use Spwa\Template\Node;
use Spwa\Template\NodePath;
use Spwa\Template\TextNode;

class App
{
    static function render(Component $component): void
    {
        // render previous
        $state = new PathState();
        $view = $component->view();
        $prev = $view->render(NodePath::root(), $state);

        // find event from frontend.
        // execute event that will likely change the dom
        $handler = $state->getEvent(new NodePath([0, 2, 0]), "click");
        if ($handler) {
            $handler();
        }

        // render again
        $next = $component->view();
        Node::compareNode($view, $next);

        echo $next->render(NodePath::root(), new PathState())->render();
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