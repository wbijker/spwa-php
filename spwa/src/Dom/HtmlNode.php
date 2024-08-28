<?php

namespace Spwa\Dom;

use Spwa\Patch;
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

    static function compareNodes(HtmlNode $prev, HtmlNode $next, array &$patches): void
    {
        if ($prev instanceof HtmlText && $next instanceof HtmlText) {
            HtmlText::compare($prev, $next, $patches);
            return;
        }

        if ($prev instanceof HtmlElement && $next instanceof HtmlElement) {
            HtmlElement::compare($prev, $next, $patches);
            return;
        }

        if ($prev instanceof HtmlEach && $next instanceof HtmlEach) {
            HtmlEach::compare($prev, $next, $patches);
            return;
        }

        $patches[] = Patch::replace($prev->path, $next);
    }
}

