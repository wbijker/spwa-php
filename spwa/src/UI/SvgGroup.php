<?php

namespace Spwa\UI;

class SvgGroup extends SvgElement
{
    /** @var SvgElement[] */
    protected array $children = [];

    public function content(SvgElement ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function toNode(): Node
    {
        $node = Node::el('g');
        $this->applyCommonAttrs($node);

        foreach ($this->children as $child) {
            $node->children($child->toNode());
        }

        return $node;
    }
}
