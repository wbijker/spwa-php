<?php

namespace BrickPHP\VNode;

use BrickPHP\State\StateManager;
use BrickPHP\UI\DomNode;
use BrickPHP\UI\NoOpDomNode;

/**
 * A virtual node that skips the diffing algorithm.
 * Renders to NoOpDomNode which does nothing when compared.
 */
class NoOpNode extends VNode
{
    public function render(StateManager $state, ?VNode $parent = null, RenderPhase $phase = RenderPhase::Initial): DomNode
    {
        $this->parent = $parent;
        if (empty($this->path)) {
            $this->path = $parent?->getPath() ?? [];
        }

        return new NoOpDomNode();
    }

    public function finalize(StateManager $state): void
    {
        // Nothing to finalize
    }
}
