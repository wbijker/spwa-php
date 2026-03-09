<?php

namespace Spwa\UI;

/**
 * Label element.
 */
class Label extends UIElement
{
    protected ?string $for = null;
    /** @var (UIElement|string)[] */
    protected array $children = [];

    public function __construct(?string $text = null)
    {
        if ($text !== null) {
            $this->children[] = $text;
        }
    }

    public function for(string $id): static
    {
        $this->for = $id;
        return $this;
    }

    public function content(UIElement|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function render(): DomNode
    {
        $node = $this->node('label');

        if ($this->for !== null) {
            $node->attr('for', $this->for);
        }

        foreach ($this->children as $child) {
            if ($child instanceof UIElement) {
                $node->children($child->render());
            } else {
                $node->children($child);
            }
        }

        return $node;
    }
}
