<?php

namespace Spwa;

use Spwa\Template\Component;
use Spwa\Template\EventListeners;
use Spwa\Template\Node;
use Spwa\Template\NodePath;
use Spwa\Template\TextNode;

class App
{
    static function render(Component $component): void
    {
        // render previous
        $prevListeners = new EventListeners();
        $view = $component->view();
        $prev = $view->render(NodePath::root(), $prevListeners);

        // find event from frontend.
        // execute event that will likely change the dom
        $event = $prevListeners->getEvent("click", new NodePath([0, 2, 0]));
        if ($event) {
            ($event->handler)();
        }

        // render again
        $next = $component->view();
        Node::compareNode($view, $next);

        // abstract function compare(HtmlNode $next, Patches $patches): void;
//        $prev->compare($prev);

        // render the changes
//        $next = $component->render(NodePath::root(), $prevListeners);

        // 1. dont't construct a new component every time. / replace the new component with the old one
        // 2. HtmlNode::rebuild() -> rebuild the dom with the new dom. function rebuild render(): () -> HtmlNode;
        // 3. Walk HtmlNodes, and rebuild.

        // Chang is made on the old node instance.
        // Event is attached there.
        // then old event trigger a change on the app state - which then change the render output.


//        echo $prev->render();
//
//        echo "<hr>";
//
//        echo $next->render();
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