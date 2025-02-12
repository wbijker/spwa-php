<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;

class HtmlDocument extends HtmlNode
{
    public function __construct(
        string $lang,
        array  $head,
        Node   $body
    )
    {
        $this->setAttrs([
            "lang" => $lang
        ]);

        $this->children = [
            new Head($head),
            new Body([$body])
        ];
    }

//    function renderHtml(RenderContext $context): DomNode
//    {
//        return new HtmlDomNode($this, $context->current, $this->tag(), $this->attrs, $this->children);
//
//        return "<!DOCTYPE html>" . PHP_EOL . parent::renderHtml($context);
//    }

    function tag(): string
    {
        return 'html';
    }

//    function initialize(?Node $parent, PathInfo $path, StateManager $manager): void
//    {
//        foreach ($this->children as $child) {
//            $child->initialize($this, $path, $manager);
//        }
//    }


}