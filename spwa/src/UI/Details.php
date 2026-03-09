<?php

namespace Spwa\UI;

/**
 * Details/summary disclosure element.
 */
class Details extends UIElement
{
    protected ?string $summary = null;
    /** @var UIElement[] */
    protected array $children = [];
    protected bool $open = false;

    public function summary(string $summary): static
    {
        $this->summary = $summary;
        return $this;
    }

    public function content(UIElement ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function open(bool $open = true): static
    {
        $this->open = $open;
        return $this;
    }

    public function render(): DomNode
    {
        $node = $this->node('details');

        if ($this->open) {
            $node->attr('open', 'open');
        }

        if ($this->summary !== null) {
            $node->children(DomNode::el('summary')->children($this->summary));
        }

        foreach ($this->children as $child) {
            $node->children($child->render());
        }

        return $node;
    }
}
