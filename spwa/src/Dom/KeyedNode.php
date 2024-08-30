<?php

namespace Spwa\Dom;

class KeyedNode
{
    /**
     * @var int|string
     */
    public $key;
    public HtmlNode $node;

    public function __construct($key, HtmlNode $node)
    {
        $this->key = $key;
        $this->node = $node;
    }
}