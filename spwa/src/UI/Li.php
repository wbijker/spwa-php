<?php

namespace Spwa\UI;

/**
 * List item element.
 */
class Li extends UIElementContent
{
    protected ?int $value = null;

    public function __construct(string|UIElement|null $content = null)
    {
        parent::__construct('li');
        if ($content !== null) {
            $this->children[] = $content;
        }
    }

    public function value(int $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('li');

        if ($this->value !== null) {
            $node->attr('value', (string)$this->value);
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
