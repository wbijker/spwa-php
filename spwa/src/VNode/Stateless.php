<?php

namespace Spwa\VNode;

use Spwa\State\StateManager;
use Spwa\UI\DomNode;
use Spwa\UI\TagDomNode;
use Spwa\UI\UIElement;

/**
 * A component with no state and no lifecycle. Subclasses implement a
 * single build() that turns constructor arguments into a VNode tree —
 * the useState/created/updated/deleted/shouldRender surface that
 * Component exposes is deliberately absent here.
 *
 * Usage mirrors Component: extend, take props through the constructor,
 * return a VNode from build(). Use this for purely visual building
 * blocks whose output is a pure function of their props (headings,
 * cards, layout wrappers, icons).
 */
abstract class Stateless extends VNode
{
    abstract protected function build(): VNode;

    public function render(StateManager $state, ?VNode $parent = null, RenderPhase $phase = RenderPhase::Initial): DomNode
    {
        return $this->build()->render($state, $parent, $phase);

//        $this->parent = $parent;
//        if (empty($this->path)) {
//            $this->path = $parent?->getPath() ?? [];
//        }
//
//        $child = $this->build();
//        $rendered = $child->render($state, $this, $phase);
//
//        // Mirror Component's dev-mode stamp so the inspector/wireframe
//        // shows the Stateless class (and ctrl+click jumps to its file),
//        // not whatever UI element happens to be its build root.
//        if ($rendered instanceof TagDomNode && UIElement::$captureSource) {
//            $cls = static::class;
//            $short = ($pos = strrpos($cls, '\\')) !== false ? substr($cls, $pos + 1) : $cls;
//            $rendered->wireframeLabel = $short;
//
//            $rc = new \ReflectionClass(static::class);
//            $file = $rc->getFileName() ?: null;
//            $rendered->wireframeFile = $file !== null ? UIElement::mapHostPath($file) : null;
//            $rendered->wireframeLine = $rc->getStartLine() ?: null;
//        }
//
//        return $rendered;
    }

    public function finalize(StateManager $state): void
    {
    }
}
