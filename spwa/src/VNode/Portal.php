<?php

namespace Spwa\VNode;

use Spwa\State\StateManager;
use Spwa\UI\DomNode;
use Spwa\UI\NoOpDomNode;
use Spwa\UI\TagDomNode;
use Spwa\UI\UIElement;

/**
 * Renders content into a {@see PortalTarget} elsewhere in the tree
 * instead of at the Portal's own location. The Portal itself emits no
 * DOM at its declaration site (it returns a NoOpDomNode) — its
 * contributions are appended to the target's DomNode in the order
 * portals are walked during render.
 *
 *   return new Portal(target: MyApp::$modal, key: 'login-dialog')
 *       ->content(UI::text('I render in the modal layer'));
 *
 * `key` is required and must be unique among Portals sharing a target.
 * Each Portal renders a single keyed wrapper element into the target,
 * which switches the target's child diff to keyed matching — so
 * Portals that appear/disappear conditionally diff cleanly instead of
 * shifting positions onto each other.
 */
class Portal extends VNode
{
    /** @var (VNode|UIElement|DomNode|string)[] */
    private array $children = [];

    public function __construct(
        private readonly PortalTarget $target,
        private readonly string $key,
        private readonly string $tag = 'div',
    ) {}

    public function content(VNode|UIElement|DomNode|string|null ...$children): static
    {
        foreach ($children as $child) {
            if ($child !== null) {
                $this->children[] = $child;
            }
        }
        return $this;
    }

    public function render(StateManager $state, ?VNode $parent = null, RenderPhase $phase = RenderPhase::Initial): DomNode
    {
        $this->parent = $parent;
        if (empty($this->path)) {
            $this->path = $parent?->getPath() ?? [];
        }

        $targetDom = PortalTarget::find($this->target->name);
        if ($targetDom === null) {
            return new NoOpDomNode();
        }

        $targetPath = $targetDom->getPath();
        $slot = count($targetDom->getChildren());
        $wrapperPath = [...$targetPath, $slot];

        $wrapper = (new TagDomNode($this->tag))
            ->key($this->key)
            ->attr('data-portal-source', $this->key);
        $wrapper->assignPaths($wrapperPath);

        $domChildren = [];
        $index = 0;
        foreach ($this->children as $child) {
            if ($child instanceof VNode) {
                $child->setPath([...$wrapperPath, $index]);
                $domChildren[] = $child->render($state, $this, $phase);
            } elseif ($child instanceof UIElement) {
                $child->setPath([...$wrapperPath, $index]);
                $domChildren[] = $child->render($state, $this, $phase);
            } elseif ($child instanceof DomNode) {
                $child->assignPaths([...$wrapperPath, $index]);
                $domChildren[] = $child;
            } else {
                $domChildren[] = $child;
            }
            $index++;
        }

        $wrapper->content(...$domChildren);
        $targetDom->content($wrapper);

        return new NoOpDomNode();
    }

    public function finalize(StateManager $state): void
    {
    }
}
