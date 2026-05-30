<?php

namespace BrickPHP\VNode;

use BrickPHP\State\StateManager;
use BrickPHP\UI\DomNode;
use BrickPHP\UI\NoOpDomNode;

/**
 * Common scaffolding shared by Component and StatelessComponent — the
 * minimal surface every "component" provides:
 *
 *   - build()        — produce the VNode subtree for this position.
 *   - render()       — drive the diff/lifecycle flow.
 *   - created()      — hook fired on first appearance.
 *   - shouldRender() — opt-in memoization hook for the Patch phase.
 *   - finalize()     — post-render cleanup (default no-op).
 *
 * The render() defined here is the simple stateless flow:
 * Initial → fire created. Patch → look up the prior instance; if it
 * existed and shouldRender() returns false, return a NoOpDomNode and
 * keep the existing subtree. DiffOld → just register, no hooks.
 *
 * Component overrides render() with a richer flow that also runs state
 * restoration, the additional lifecycle hooks (restored / updated /
 * deleted), and dev-mode wireframe metadata stamping. StatelessComponent
 * inherits everything as-is.
 */
abstract class BaseComponent extends VNode
{
    /** @var array<string, BaseComponent> Instances seen during Initial / DiffOld, keyed by path:class. Consulted in Patch. */
    private static array $oldRegistry = [];

    abstract protected function build(): VNode;

    /**
     * Fires the first time this component appears at its tree position
     * in a diff cycle:
     *
     *   - Initial GET: fires for every component (the whole tree is new).
     *   - POST Patch:  fires only when this path:class wasn't present in
     *                  the OLD tree (a newly inserted component).
     *   - POST DiffOld: never fires — that phase replays an existing tree.
     */
    protected function created(): void
    {
    }

    /**
     * Fires after build() has produced this component's DomNode subtree,
     * once per real render. Use it for side effects that depend on the
     * component actually being in the rendered tree — most notably
     * Js::run calls that must queue AFTER whatever created() emits
     * (so creation runs first in the Brick.ready block).
     *
     * Skipped during DiffOld (that phase only replays the OLD tree for
     * diffing, and shouldn't queue user-visible side effects). Also
     * skipped when shouldRender() returns false (no build happened).
     *
     * Compared to created(), which fires once on first appearance,
     * rendered() fires every render — so component instances that are
     * constructed but never placed in the tree (e.g. an eagerly-evaluated
     * fallback expression in a router) never trigger their side effects.
     */
    protected function rendered(DomNode $dom): void
    {
    }

    /**
     * Decide whether this component needs to be re-rendered in the
     * current diff cycle. Receives the matching instance from the OLD
     * render at the same path — typically you compare props/state and
     * short-circuit when nothing relevant has changed.
     *
     * Return `false` to skip both rendering AND diffing for this
     * subtree: the framework substitutes a NoOpDomNode whose compare()
     * is a no-op, so the frontend keeps the existing DOM. Default is
     * `true` (re-render every cycle).
     *
     * Only consulted in RenderPhase::Patch.
     *
     * @param static $old The OLD instance at the same path — always
     *   the same concrete class as `$this`, safe to narrow in overrides
     *   via `@param MySubclass $old`.
     */
    protected function shouldRender(self $old): bool
    {
        return true;
    }

    public function render(StateManager $state, ?VNode $parent = null, RenderPhase $phase = RenderPhase::Initial): DomNode
    {
        $key = $this->getStateKey();

        if ($phase === RenderPhase::Patch) {
            $old = self::$oldRegistry[$key] ?? null;
            if ($old === null) {
                // Not in OLD tree → this is a fresh insertion.
                $this->created();
            } elseif (!$this->shouldRender($old)) {
                return new NoOpDomNode();
            }
        } else {
            // Initial fires created (whole tree is new). DiffOld is just
            // replaying an existing tree, so no created.
            if ($phase === RenderPhase::Initial) {
                $this->created();
            }
            self::$oldRegistry[$key] = $this;
        }

        $dom = $this->build()->render($state, $parent, $phase);

        // Skip the rendered() hook in DiffOld — that phase just replays
        // the prior tree for diffing and shouldn't side-effect.
        if ($phase !== RenderPhase::DiffOld) {
            $this->rendered($dom);
        }

        return $dom;
    }

    public function finalize(StateManager $state): void
    {
    }
}
