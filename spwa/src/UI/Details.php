<?php

namespace Spwa\UI;

use Spwa\VNode\VNode;

/**
 * Details/summary disclosure element.
 */
class Details extends UIElement
{
    protected ?string $summary = null;
    /** @var (DomNode|VNode|string)[] */
    protected array $children = [];
    protected bool $open = false;

    public function summary(string $summary): static
    {
        $this->summary = $summary;
        return $this;
    }

    public function content(DomNode|VNode|string ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function open(bool $open = true): static
    {
        $this->open = $open;
        return $this;
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('details');

        if ($this->open) {
            $node->attr('open', 'open');
        }

        if ($this->summary !== null) {
            $node->children(DomNode::el('summary')->children($this->summary));
        }

        foreach ($this->children as $child) {
            if ($child instanceof UIElement) {
                $node->children($child->build());
            } elseif ($child instanceof DomNode) {
                $node->children($child);
            } elseif (is_string($child)) {
                $node->children($child);
            }
        }

        return $node;
    }
}
