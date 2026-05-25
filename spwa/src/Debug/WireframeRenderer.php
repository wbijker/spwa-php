<?php

namespace Spwa\Debug;

use Spwa\UI\DomNode;
use Spwa\UI\TagDomNode;
use Spwa\UI\TextDomNode;

/**
 * Wireframe view of a rendered DOM tree. Triggered by `?wireframe=true`
 * in Spwa::handleGet — kept entirely off the hot path, so production
 * renders never touch it.
 *
 * Mutates the tree in place:
 *
 *   - every TagDomNode picks up `.spwa-wf` + a `<span class="spwa-wf-label">`
 *     prepended as its first child; the data-wf-{label,file,line} attrs
 *     are stamped earlier by UIElement/Component and auto-emitted in
 *     TagDomNode::toHtml, so this layer doesn't need to write them.
 *   - `<img>` collapses into a placeholder div with diagonal cross-lines and
 *     an "image" label, keeping the original sizing classes intact
 *   - every TextDomNode rewrites its content to the literal string "text"
 *
 * Event handlers are stripped — wireframe mode is read-only. The inspect
 * bootstrap JS still picks up ctrl+click for the editor jump.
 */
class WireframeRenderer
{
    /** Tags we leave entirely alone (their content isn't user-facing, or
     *  the wrapping would break them). */
    private const SKIP = [
        'script' => 1, 'style' => 1, 'link' => 1, 'meta' => 1, 'title' => 1,
        'head' => 1, 'html' => 1,
        // SVG subtree — wrapping would break the namespace.
        'svg' => 1, 'path' => 1, 'circle' => 1, 'rect' => 1, 'g' => 1,
        'polygon' => 1, 'polyline' => 1, 'ellipse' => 1, 'line' => 1,
        'defs' => 1, 'use' => 1, 'symbol' => 1,
    ];

    /** Tags that have no children and can't host a label child. */
    private const VOID = [
        'br' => 1, 'hr' => 1, 'input' => 1, 'meta' => 1, 'link' => 1,
        'area' => 1, 'base' => 1, 'col' => 1, 'embed' => 1, 'source' => 1,
        'track' => 1, 'wbr' => 1,
    ];

    public static function transform(DomNode $node): DomNode
    {
        if ($node instanceof TextDomNode) {
            return new TextDomNode('text');
        }
        if (!$node instanceof TagDomNode) {
            return $node;
        }

        $tag = $node->getTag();

        if (isset(self::SKIP[$tag])) {
            return $node;
        }

        if ($tag === 'img') {
            return self::imageBox($node);
        }

        $newChildren = [];
        foreach ($node->getChildren() as $child) {
            $newChildren[] = $child instanceof DomNode ? self::transform($child) : $child;
        }
        $node->clearChildren();

        if (isset(self::VOID[$tag])) {
            $node->content(...$newChildren);
            return $node;
        }

        // Wireframe is read-only: a stray plain-click shouldn't navigate the
        // app while you're inspecting. Ctrl+click is intercepted by the
        // INSPECT_JS handler.
        $node->clearEvents();

        self::addWireframeClass($node);

        $label = $node->wireframeLabel ?? $tag;
        $labelNode = (new TagDomNode('span'))
            ->attr('class', 'spwa-wf-label')
            ->rawContent(htmlspecialchars($label, ENT_QUOTES));

        $node->content($labelNode, ...$newChildren);
        return $node;
    }

    /**
     * <img src> → <div class="spwa-wf spwa-wf-img">. The original sizing
     * classes (width/height/object-fit) stay on the div so the placeholder
     * fills the same box; src/alt linger as harmless attributes.
     */
    private static function imageBox(TagDomNode $node): TagDomNode
    {
        $node->setTag('div');
        self::addWireframeClass($node, 'spwa-wf-img');

        $labelNode = (new TagDomNode('span'))
            ->attr('class', 'spwa-wf-label')
            ->rawContent('image');

        $node->clearChildren();
        $node->content($labelNode);
        return $node;
    }

    /**
     * Append `.spwa-wf` (and any extra class) to the node's class
     * attribute. The data-wf-{label,file,line} attrs are emitted
     * automatically by TagDomNode::toHtml whenever the fields are set,
     * so we don't write them through getAttributes here.
     */
    private static function addWireframeClass(TagDomNode $node, string $extraClass = ''): void
    {
        $attrs = $node->getAttributes();
        $existing = $attrs['class'] ?? '';
        $merged = trim($existing . ' spwa-wf' . ($extraClass !== '' ? ' ' . $extraClass : ''));
        $node->attr('class', $merged);
    }
}
