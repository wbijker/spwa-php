<?php

namespace Spwa\Dom;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\PathInfo;

class TextDomNode extends DomNode
{

    public function __construct(
        public Node     $owner,
        public PathInfo $path,
        public string   $text)
    {
        parent::__construct($owner, $path);

    }

    public function renderHtml(): string
    {
        return '(' . $this->path->pathStr() . ') ' . $this->text;
//        return htmlspecialchars($this->text)
//        return htmlentities($this->text);
    }
}

