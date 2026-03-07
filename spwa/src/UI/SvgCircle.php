<?php

namespace Spwa\UI;

class SvgCircle extends SvgElement
{
    public function __construct(
        protected float $cx,
        protected float $cy,
        protected float $r
    ) {
    }

    public function toNode(): Node
    {
        $node = Node::el('circle')
            ->attr('cx', (string)$this->cx)
            ->attr('cy', (string)$this->cy)
            ->attr('r', (string)$this->r);
        $this->applyCommonAttrs($node);
        return $node;
    }
}
