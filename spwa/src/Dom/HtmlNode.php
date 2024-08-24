<?php

namespace Spwa\Dom;

use Spwa\Template\Node;
use Spwa\Template\NodePath;

abstract class HtmlNode
{
    protected Node $owner;
    protected NodePath $path;

    public function __construct(Node $owner, NodePath $path)
    {
        $this->owner = $owner;
        $this->path = $path;
    }

    abstract function render(): string;
}

