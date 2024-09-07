<?php

namespace Spwa\Dom;

use Spwa\Patch;
use Spwa\Template\Node;
use Spwa\Template\NodePath;

class HtmlEach extends HtmlNode
{
    /**
     * @var KeyedNode[] $children
     */
    private array $children;

    /**
     * @param Node $owner
     * @param NodePath $path
     * @param KeyedNode[] $children
     */
    public function __construct(Node $owner, NodePath $path, array $children = [])
    {
        parent::__construct($owner, $path);
        $this->children = $children;
    }

    public static function compare(HtmlEach $prev, HtmlEach $next, array &$patches)
    {
        // compare keys of the children
        // difference return in reverse order.
        // Adjust the DOM from last to first to avoid index issues
        $diff = Levenshtein::diff($prev->children, $next->children, fn($node) => $node->key);
        foreach ($diff as $item) {
            [$action, $from, $to] = $item;
            switch ($action) {
                case Levenshtein::DELETE:
                    $patches[] = Patch::delete($from->node->path);
                    break;
                case Levenshtein::INSERT:
                    $patches[] = Patch::insert($to->node->path, $to->node);
                    break;
                case Levenshtein::SUBSTITUTE:
                    $patches[] = Patch::replace($from->node->path, $to->node);
                    break;
            }
        }
    }

    function render(): string
    {
        return implode("", array_map(fn(KeyedNode $child) => $child->node->render(), $this->children));
    }
}