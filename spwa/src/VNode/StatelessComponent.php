<?php

namespace Spwa\VNode;

use Spwa\State\StateManager;
use Spwa\UI\DomNode;
use Spwa\UI\NoOpDomNode;
use Spwa\UI\TagDomNode;
use Spwa\UI\UIElement;

/**
 * A component with no state and no lifecycle. Subclasses implement a
 * single build() that turns constructor arguments into a VNode tree —
 * the useState/created/updated/deleted surface that Component exposes
 * is deliberately absent here. shouldRender() is available as an
 * optional memoization hook in the Patch phase.
 *
 * Usage mirrors Component: extend, take props through the constructor,
 * return a VNode from build(). Use this for purely visual building
 * blocks whose output is a pure function of their props (headings,
 * cards, layout wrappers, icons).
 */
abstract class StatelessComponent extends VNode
{
    /** @var array<string, StatelessComponent> Instances seen during the OLD (Initial / DiffOld) phase, keyed by path */
    private static array $oldRegistry = [];

    abstract protected function build(): VNode;

    /**
     * In the Patch phase, decide whether this component needs to be
     * rebuilt given its prior instance at the same path. Return false
     * to keep the existing subtree — the framework substitutes a
     * NoOpDomNode whose compare() emits no patches. Default is `true`
     * (rebuild every cycle); override and compare constructor-arg
     * props to opt into memoization.
     *
     * @param static $old The OLD instance at the same path — always
     *   the same concrete class as `$this`.
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
            if ($old !== null && !$this->shouldRender($old)) {
                return new NoOpDomNode();
            }
        } else {
            self::$oldRegistry[$key] = $this;
        }

        return $this->build()->render($state, $parent, $phase);
    }

    public function finalize(StateManager $state): void
    {
    }
}
