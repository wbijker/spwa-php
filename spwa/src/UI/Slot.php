<?php

namespace Spwa\UI;

/**
 * Slot element for web components.
 */
class Slot extends UIElement
{
    protected ?string $name = null;

    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('slot');

        if ($this->name !== null) {
            $node->attr('name', $this->name);
        }

        return $node;
    }
}
