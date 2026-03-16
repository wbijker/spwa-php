<?php

namespace Spwa\UI;

/**
 * Label element.
 */
class Label extends UIElementContent
{
    protected ?string $for = null;

    public function __construct(?string $text = null)
    {
        parent::__construct('label');
        if ($text !== null) {
            $this->children[] = $text;
        }
    }

    public function for(string $id): static
    {
        $this->for = $id;
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('label');

        if ($this->for !== null) {
            $node->attr('for', $this->for);
        }

        foreach ($this->children as $child) {
            if ($child instanceof UIElement) {
                $node->children($child->build());
            } else {
                $node->children($child);
            }
        }

        return $node;
    }
}
