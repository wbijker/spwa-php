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

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('dialog');

        if ($this->open) {
            $node->attr('open', 'open');
        }

        foreach ($this->children as $child) {
            $node->children($child->build());
        }

        return $node;
    }
}
