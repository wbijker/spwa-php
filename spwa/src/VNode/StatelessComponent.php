<?php

namespace Spwa\VNode;

/**
 * A component with no state and no lifecycle beyond what BaseComponent
 * exposes. Subclasses implement a single build() that turns constructor
 * arguments into a VNode tree; the useState / restored / updated /
 * deleted surface that Component layers on top is deliberately absent.
 *
 * Usage mirrors Component: extend, take props through the constructor,
 * return a VNode from build(). Optional hooks inherited from
 * BaseComponent: created() fires on first appearance, shouldRender()
 * provides opt-in memoization in the Patch phase.
 *
 * Use this for purely visual building blocks whose output is a pure
 * function of their props (headings, cards, layout wrappers, icons).
 */
abstract class StatelessComponent extends BaseComponent
{
}
