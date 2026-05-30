<?php

namespace BrickPHP\VNode;

use BrickPHP\State\StateManager;
use BrickPHP\UI\DomNode;
use BrickPHP\UI\TagDomNode;

/**
 * A named slot in the tree that Portals can render into from anywhere
 * else in the same tree.
 *
 *   class MyApp extends App {
 *       public static PortalTarget $modal;
 *       protected function view(): VNode {
 *           self::$modal = new PortalTarget(name: 'modal-layer');
 *           return UI::column()->content(
 *               $mainContent,
 *               self::$modal,
 *           );
 *       }
 *   }
 *
 *   // Anywhere deeper in the tree:
 *   (new Portal(target: MyApp::$modal))->content(UI::text('hi'));
 *
 * Render order matters: the target must render before any Portal aimed
 * at it. In practice that means place targets high (or late as a
 * sibling) and portals deeper.
 */
class PortalTarget extends VNode
{
    /** @var array<string, TagDomNode> name => the live DomNode of the most recently rendered target */
    private static array $registry = [];

    public function __construct(
        public readonly string $name,
        private readonly string $tag = 'div',
    ) {}

    /**
     * Look up the live target DomNode for the given name in the current
     * render pass. Returns null if no target with that name has rendered
     * yet (or at all) in this pass.
     */
    public static function find(string $name): ?TagDomNode
    {
        return self::$registry[$name] ?? null;
    }

    /**
     * Drop all registered targets. Called by Brick between render passes
     * so a target that exists in OLD but not in NEW (or vice versa)
     * doesn't leak a stale DomNode into the other pass.
     */
    public static function reset(): void
    {
        self::$registry = [];
    }

    public function render(StateManager $state, ?VNode $parent = null, RenderPhase $phase = RenderPhase::Initial): DomNode
    {
        $this->parent = $parent;
        if (empty($this->path)) {
            $this->path = $parent?->getPath() ?? [];
        }

        $dom = (new TagDomNode($this->tag))->attr('data-portal', $this->name);
        $dom->assignPaths($this->path);

        self::$registry[$this->name] = $dom;

        return $dom;
    }

    public function finalize(StateManager $state): void
    {
    }
}
