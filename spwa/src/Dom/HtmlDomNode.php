<?php

namespace Spwa\Dom;

use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\PathInfo;

class HtmlDomNode extends DomNode
{
    /**
     * @param Node $owner
     * @param HtmlNode|null $parent
     * @param PathInfo $path
     * @param string $tag
     * @param array $attrs
     * @param DomNode[] $children
     */
    public function __construct(
        public Node      $owner,
        public PathInfo  $path,

        public string    $tag,
        public array     $attrs = [],
        public array     $children = [])
    {
        parent::__construct($owner, $path);

    }

    private static array $selfClosingTags = ['img', 'input', 'br', 'hr', 'meta', 'link', 'base', 'col', 'area', 'param', 'command', 'keygen', 'source'];

    function closed(): bool
    {
        return in_array($this->tag, self::$selfClosingTags);
    }

    function renderHtml(): string
    {
        $tag = $this->tag;
        $ret = "<$tag";

        $copy = $this->attrs;

        if ($this->path != null) {
            $copy['path'] = $this->path->pathStr();
            $copy['key'] = $this->path->keyStr();
        }

        foreach ($copy as $key => $value) {
            $ret .= " $key=\"$value\"";
        }

        if ($this->closed()) {
            $ret .= "/>";
            return $ret;
        }

        $ret .= ">";
        foreach ($this->children as $child) {
            $ret .= $child->renderHtml();
        }
        $ret .= "</$tag>";
        return $ret;
    }
}