<?php

namespace Spwa\Dom;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\PathInfo;

class CommentDomNode extends DomNode
{

    public function __construct(
        public Node      $owner,
        public ?HtmlNode $parent,
        public PathInfo  $path,
        public string    $text)
    {
        parent::__construct($owner, $parent, $path);

    }

    public function renderHtml(): string
    {
        return '<!--' . htmlspecialchars($this->text) . '-->';
    }
}

