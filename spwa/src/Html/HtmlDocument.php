<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;

class HtmlDocument extends HtmlNode
{
    public function __construct(
        string       $lang,
        array        $head,
        private Node $body
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

    function find(array $path): ?Node
    {
        $key = array_shift($path);
        if ($key == 0) {
            return $this->body->find($path);
        }
        return null;
    }

    function renderHtml(): string
    {
        return "<!DOCTYPE html>" . PHP_EOL . parent::renderHtml();
    }

    function tag(): string
    {
        return 'html';
    }



}