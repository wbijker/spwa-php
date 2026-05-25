<?php

namespace Spwa\Debug;

use Spwa\UI\DomNode;
use Spwa\UI\TagDomNode;
use Spwa\UI\TextDomNode;

/**
 * Wireframe / skeleton view of a rendered DOM tree. Triggered by
 * `?skeleton=true` in Spwa::handleGet — kept entirely off the hot path,
 * so production renders never touch it.
 *
 * Mutates the tree in place:
 *
 *   - every TagDomNode picks up `.spwa-skel` + a `<span class="spwa-skel-label">`
 *     prepended as its first child, plus data-skel-{label,file,line} attrs
 *     for the frontend ctrl+click handler
 *   - `<img>` collapses into a placeholder div with diagonal cross-lines and
 *     an "image" label, keeping the original sizing classes intact
 *   - every TextDomNode rewrites its content to the literal string "text"
 *
 * Event handlers are not stripped here — the skeleton bootstrap JS swallows
 * non-ctrl clicks in capture phase, so the page becomes inspectable without
 * disturbing the underlying handlers.
 */
class SkeletonRenderer
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

        // Recurse into children. Mutates the node's children array in place
        // via clearChildren() + content().
        $newChildren = [];
        foreach ($node->getChildren() as $child) {
            $newChildren[] = $child instanceof DomNode ? self::transform($child) : $child;
        }
        $node->clearChildren();

        // Void elements: keep them as-is, no label (no children allowed).
        if (isset(self::VOID[$tag])) {
            $node->content(...$newChildren);
            return $node;
        }

        // Skeleton is read-only: a stray plain-click shouldn't navigate the
        // app while you're inspecting. Ctrl+click is intercepted by the JS
        // bootstrap below.
        $node->clearEvents();

        $label = $node->skeletonLabel ?? $tag;
        self::tagAsSkeleton($node, $label);

        // Label always at the top so it stacks above the content. Position
        // is handled in CSS (absolute top:0 left:0).
        $labelNode = (new TagDomNode('span'))
            ->attr('class', 'spwa-skel-label')
            ->rawContent(htmlspecialchars($label, ENT_QUOTES));

        $node->content($labelNode, ...$newChildren);
        return $node;
    }

    /**
     * <img src> → <div class="spwa-skel spwa-skel-img">. The original sizing
     * classes (width/height/object-fit) stay on the div so the placeholder
     * fills the same box; src/alt linger as harmless attributes.
     */
    private static function imageBox(TagDomNode $node): TagDomNode
    {
        $node->setTag('div');
        self::tagAsSkeleton($node, $node->skeletonLabel ?? 'image', 'spwa-skel-img');

        $labelNode = (new TagDomNode('span'))
            ->attr('class', 'spwa-skel-label')
            ->rawContent('image');

        $node->clearChildren();
        $node->content($labelNode);
        return $node;
    }

    /**
     * Append `.spwa-skel` (and any extra class) to the node's class
     * attribute and stamp data-skel-{label,file,line} so the frontend
     * inspector can read them on ctrl+click.
     */
    private static function tagAsSkeleton(TagDomNode $node, string $label, string $extraClass = ''): void
    {
        $attrs = $node->getAttributes();
        $existing = $attrs['class'] ?? '';
        $merged = trim($existing . ' spwa-skel' . ($extraClass !== '' ? ' ' . $extraClass : ''));
        $node->attr('class', $merged);

        $node->attr('data-skel-label', $label);
        if ($node->skeletonFile !== null) {
            $node->attr('data-skel-file', $node->skeletonFile);
        }
        if ($node->skeletonLine !== null) {
            $node->attr('data-skel-line', (string)$node->skeletonLine);
        }
    }
}
