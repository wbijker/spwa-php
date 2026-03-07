<?php

namespace Spwa\UI;

/**
 * Dialog element.
 */
class Dialog extends Container
{
    protected bool $open = false;

    public function open(bool $open = true): static
    {
        $this->open = $open;
        return $this;
    }

    public function render(): Node
    {
        $node = $this->node('dialog');

        if ($this->open) {
            $node->attr('open', 'open');
        }

        foreach ($this->children as $child) {
            $node->children($child->render());
        }

        return $node;
    }
}
